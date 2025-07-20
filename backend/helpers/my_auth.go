package helpers

import (
	"github.com/golang-jwt/jwt/v5"
	"github.com/labstack/echo/v4"
)

type JWTClaims struct {
	UserID string `json:"user_id"`
	jwt.RegisteredClaims
}

func GetUserClaims(c echo.Context) *JWTClaims {
	u := c.Get("user")
	if u == nil {
		return nil
	}

	token, ok := u.(*jwt.Token)
	if !ok || token == nil {
		return nil
	}

	if cl, ok := token.Claims.(*JWTClaims); ok {
		return cl
	}

	if mc, ok := token.Claims.(jwt.MapClaims); ok {
		if id, ok := mc["user_id"].(string); ok {
			return &JWTClaims{UserID: id}
		}
	}

	return nil
}
