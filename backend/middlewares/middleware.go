package middlewares

import (
	"errors"
	"fmt"
	"net/http"
	"os"
	"strings"
	"time"

	"github.com/golang-jwt/jwt/v5"
	echojwt "github.com/labstack/echo-jwt/v4"
	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
	"github.com/muhammadridwansurya/api-go/helpers"
	"golang.org/x/time/rate"
)

type Middlewares struct {
	KeyApi echo.MiddlewareFunc
	JWT    echo.MiddlewareFunc
}

func InitRateLimiterConfig() middleware.RateLimiterConfig {
	return middleware.RateLimiterConfig{
		Skipper: middleware.DefaultSkipper,
		Store: middleware.NewRateLimiterMemoryStoreWithConfig(
			middleware.RateLimiterMemoryStoreConfig{
				Rate:      rate.Limit(10),
				Burst:     30,
				ExpiresIn: 3 * time.Minute,
			},
		),
		IdentifierExtractor: func(ctx echo.Context) (string, error) {
			return ctx.RealIP(), nil
		},
		ErrorHandler: func(ctx echo.Context, err error) error {
			return ctx.JSON(http.StatusForbidden, helpers.BasicResponse(
				false,
				"forbidden",
			))
		},
		DenyHandler: func(ctx echo.Context, identifier string, err error) error {
			return ctx.JSON(http.StatusTooManyRequests, helpers.BasicResponse(
				false,
				"terlalu banyak request",
			))
		},
	}
}

func AuthKeyMiddleware(apiKey string) echo.MiddlewareFunc {
	return func(next echo.HandlerFunc) echo.HandlerFunc {
		return func(c echo.Context) error {
			authHeader := c.Request().Header.Get("X-API-Key")
			if authHeader == "" {
				return c.JSON(http.StatusUnauthorized, helpers.BasicResponse(
					false,
					"X-API-Key header missing",
				))
			}

			if authHeader != apiKey {
				return c.JSON(http.StatusUnauthorized, helpers.BasicResponse(
					false,
					"Invalid API Key",
				))
			}

			return next(c)
		}
	}
}

func InitMiddlewares() *Middlewares {
	return &Middlewares{
		JWT: initJWTMiddleware(),
	}
}

func initJWTMiddleware() echo.MiddlewareFunc {
	secret := os.Getenv("JWT_SECRET")
	if secret == "" {
		panic("JWT_SECRET environment variable not set")
	}

	return echojwt.WithConfig(echojwt.Config{
		SigningKey:  []byte(secret),
		TokenLookup: "header:Authorization",

		NewClaimsFunc: func(c echo.Context) jwt.Claims {
			return &helpers.JWTClaims{}
		},

		ErrorHandler: func(c echo.Context, err error) error {
			authHeader := c.Request().Header.Get("Authorization")
			_ = authHeader

			errorMsg := "Token error"
			switch {
			case errors.Is(err, jwt.ErrTokenMalformed):
				errorMsg = "Token format invalid"
			case errors.Is(err, jwt.ErrTokenExpired):
				errorMsg = "Token expired"
			case errors.Is(err, jwt.ErrTokenNotValidYet):
				errorMsg = "Token not yet valid"
			default:
				errorMsg = fmt.Sprintf("Token error: %v", err)
			}

			return c.JSON(http.StatusUnauthorized, helpers.BasicResponse(
				false,
				errorMsg,
			))
		},

		ParseTokenFunc: func(c echo.Context, auth string) (interface{}, error) {
			if !strings.HasPrefix(auth, "Bearer ") {
				return nil, fmt.Errorf("missing Bearer prefix")
			}

			tokenStr := strings.TrimSpace(auth[len("Bearer "):])
			parts := strings.Split(tokenStr, ".")
			if len(parts) != 3 {
				return nil, jwt.ErrTokenMalformed
			}

			return jwt.ParseWithClaims(tokenStr, &helpers.JWTClaims{}, func(t *jwt.Token) (interface{}, error) {
				if _, ok := t.Method.(*jwt.SigningMethodHMAC); !ok {
					return nil, fmt.Errorf("unexpected signing method: %v", t.Header["alg"])
				}
				return []byte(secret), nil
			})
		},
	})
}
