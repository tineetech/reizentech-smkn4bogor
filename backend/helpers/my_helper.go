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

// HashPassword hashes the password with a unique salt
func HashPassword(password string) (string, string, error) {
	// Generate a unique salt
	salt, err := generateSalt(16) // 16 bytes salt
	if err != nil {
		return "", "", fmt.Errorf("failed to generate salt: %w", err)
	}

	// Combine password and salt
	saltedPassword := password + salt + os.Getenv("AUTH_SALT")

	// Hash the salted password using bcrypt
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(saltedPassword), bcrypt.DefaultCost)
	if err != nil {
		return "", "", fmt.Errorf("failed to hash password: %w", err)
	}

	// Return the hashed password and salt
	return string(hashedPassword), salt, nil
}

// generateSalt generates a cryptographically secure random salt
func generateSalt(length int) (string, error) {
	bytes := make([]byte, length)
	if _, err := rand.Read(bytes); err != nil {
		return "", fmt.Errorf("failed to generate random bytes: %w", err)
	}
	return base64.RawStdEncoding.EncodeToString(bytes), nil
}

// ComparePassword compares the hashed password with the provided plaintext password and salt
func ComparePassword(hashedPassword, password, salt string) bool {
	// Combine password and salt
	saltedPassword := password + salt + os.Getenv("AUTH_SALT")

	// Compare the hashed password with the salted password
	err := bcrypt.CompareHashAndPassword([]byte(hashedPassword), []byte(saltedPassword))
	return err == nil
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
