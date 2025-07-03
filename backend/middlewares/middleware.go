package middlewares

import (
	"net/http"
	"os"
	"strings"
	"time"

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

// RATE LIMITER
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

// Middleware untuk mengecek auth-key
func AuthKeyMiddleware(apiKey string) echo.MiddlewareFunc {
	return func(next echo.HandlerFunc) echo.HandlerFunc {
		return func(c echo.Context) error {
			authHeader := c.Request().Header.Get("Authorization")
			if authHeader == "" {
				return c.JSON(http.StatusUnauthorized, helpers.BasicResponse(
					false,
					"Auth-Key missing",
				))
			}

			if !strings.HasPrefix(authHeader, "Bearer ") {
				return c.JSON(http.StatusUnauthorized, helpers.BasicResponse(
					false,
					"Invalid Auth-Key format",
				))
			}

			authKey := authHeader[len("Bearer "):]
			if authKey != apiKey {
				return c.JSON(http.StatusUnauthorized, helpers.BasicResponse(
					false,
					"Invalid Auth-Key",
				))
			}

			return next(c)
		}
	}
}

func InitMiddlewares() *Middlewares {
	return &Middlewares{
		KeyApi: AuthKeyMiddleware(os.Getenv("API_KEY")),
		JWT: echojwt.WithConfig(echojwt.Config{
			SigningKey:  []byte(os.Getenv("JWT_SECRET")),
			TokenLookup: "header:Authorization",
			ErrorHandler: func(c echo.Context, err error) error {
				return c.JSON(http.StatusUnauthorized, helpers.BasicResponse(
					false,
					"Invalid or expired token",
				))
			},
		}),
	}
}
