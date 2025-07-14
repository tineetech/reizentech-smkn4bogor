package helpers

import (
	"crypto/rand"
	"encoding/base64"
	"fmt"
	"os"
	"reflect"

	"github.com/labstack/echo/v4"
	"golang.org/x/crypto/bcrypt"
)

type JWTClaims struct {
	UserID   string `json:"user_id"`
	Username string `json:"username"`
	Role     string `json:"role"`
	jwt.RegisteredClaims
}

// Hash password
func HashPassword(password string) (string, error) {
	bytes, err := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)
	return string(bytes), err
}

// Verifikasi password
func CheckPasswordHash(password, hash string) bool {
	err := bcrypt.CompareHashAndPassword([]byte(hash), []byte(password))
	return err == nil
}

func GenerateJWT(secret string, userID, username, role string, duration time.Duration) (string, error) {
	claims := JWTClaims{
		UserID:   userID,
		Username: username,
		Role:     role,
		RegisteredClaims: jwt.RegisteredClaims{
			ExpiresAt: jwt.NewNumericDate(time.Now().Add(duration)),
			IssuedAt:  jwt.NewNumericDate(time.Now()),
			NotBefore: jwt.NewNumericDate(time.Now()),
			Issuer:    "ujian-app",
			Subject:   userID,
		},
	}

	token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	return token.SignedString([]byte(secret))
}

type Response struct {
	Status     bool        `json:"status"`
	Message    string      `json:"message"`
	Token      string      `json:"token,omitempty"`
	Data       interface{} `json:"data,omitempty"`
	Errors     interface{} `json:"errors,omitempty"`
	Pagination interface{} `json:"pagination,omitempty"`
}

type ValidationError struct {
	Message string            `json:"message"`
	Errors  map[string]string `json:"errors"`
}

func BasicResponse(status bool, message string) Response {
	return Response{
		Status:  status,
		Message: message,
	}
}

func AuthResponseToken(status bool, message, token string) Response {
	return Response{
		Status:  status,
		Message: message,
		Token:   token,
	}
}

func SuccessResponseWithData(status bool, message string, data interface{}) Response {
	return Response{
		Status:  status,
		Message: message,
		Data:    data,
	}
}

func SuccessResponseWithDataPagination(status bool, message string, data interface{}, pagination interface{}) Response {
	return Response{
		Status:     status,
		Message:    message,
		Data:       data,
		Pagination: pagination,
	}
}

func ErrorResponseRequest(status bool, message string, err interface{}) Response {
	return Response{
		Status:  status,
		Message: message,
		Errors:  err,
	}
}

func (e *ValidationError) Error() string {
	return e.Message
}

func BindAndValidate(ctx echo.Context, request interface{}) error {
	// Bind request
	if err := ctx.Bind(request); err != nil {
		return fmt.Errorf("invalid request payload")
	}

	// Validasi request
	if err := ctx.Validate(request); err != nil {
		requestType := reflect.TypeOf(request).Elem()
		fieldMap := MapJSONFields(requestType)
		validationErrors := HandleValidationErrors(err, fieldMap)
		return &ValidationError{
			Message: "Bad Request",
			Errors:  validationErrors,
		}
	}

	return nil
}

