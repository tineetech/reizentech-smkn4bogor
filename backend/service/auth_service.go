package service

import (
	"context"
	"fmt"
	"os"
	"strings"
	"time"

	"github.com/golang-jwt/jwt/v5"
	dto "github.com/muhammadridwansurya/api-go/dto"
	"github.com/muhammadridwansurya/api-go/helpers"
	"github.com/muhammadridwansurya/api-go/model"
	repo "github.com/muhammadridwansurya/api-go/repository"
)

const (
	jwtExpiryInMinutes = 60
)

type AuthServiceInterface interface {
	Register(ctx context.Context, req *dto.RegisterRequest) (*model.User, error)
	Login(ctx context.Context, req *dto.LoginRequest) (*model.User, string, error)
	Logout(ctx context.Context, userID string) error
	GetProfile(ctx context.Context, userID string) (*model.User, error)
	VerifyEmail(ctx context.Context, req *dto.VerifyEmailRequest) error
}
type authService struct {
	repo repo.AuthRepositoryInterface
}

func InitAuthService(r repo.AuthRepositoryInterface) AuthServiceInterface {
	return &authService{repo: r}
}

func (s *authService) Register(ctx context.Context, req *dto.RegisterRequest) (*model.User, error) {
	user, err := s.repo.Register(ctx, req)
	if err != nil {
		return nil, err
	}

	if user.Email != nil {
		token, err := GenerateEmailVerificationToken(user.ID, *user.Email)
		if err != nil {
			fmt.Printf("gagal generate token verifikasi email: %v\n", err)
		} else {
			go helpers.SendEmailVerification(*user.Email, token)
		}
	}

	return user, nil
}

func GenerateEmailVerificationToken(userID, email string) (string, error) {
	claims := jwt.MapClaims{
		"user_id": userID,
		"email":   email,
		"exp":     time.Now().Add(1 * time.Hour).Unix(),
	}

	token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	return token.SignedString([]byte(os.Getenv("JWT_EMAIL_SECRET")))
}

func (s *authService) Login(ctx context.Context, req *dto.LoginRequest) (*model.User, string, error) {
	user, err := s.repo.Login(ctx, req)
	if err != nil {
		return nil, "", err
	}

	tokenStr, err := generateJWT(user.ID)
	if err != nil {
		return nil, "", fmt.Errorf("gagal membuat token: %w", err)
	}

	if err := s.repo.UpdateRememberToken(ctx, user.ID, &tokenStr); err != nil {
	}

	return user, tokenStr, nil
}

func (s *authService) Logout(ctx context.Context, userID string) error {
	return s.repo.Logout(ctx, userID)
}

func (s *authService) GetProfile(ctx context.Context, userID string) (*model.User, error) {
	if userID == "" {
		return nil, fmt.Errorf("user ID tidak valid")
	}
	return s.repo.GetUserByID(ctx, userID)
}

func (s *authService) VerifyEmail(ctx context.Context, req *dto.VerifyEmailRequest) error {
	claims := jwt.MapClaims{}

	token, err := jwt.ParseWithClaims(req.Token, claims, func(token *jwt.Token) (interface{}, error) {
		return []byte(os.Getenv("JWT_EMAIL_SECRET")), nil
	})
	if err != nil || !token.Valid {
		return fmt.Errorf("token verifikasi tidak valid")
	}

	email, ok := claims["email"].(string)
	if !ok || email == "" {
		return fmt.Errorf("email dalam token tidak valid")
	}

	return s.repo.VerifyEmail(ctx, &dto.VerifyEmailRequest{Token: email})
}

func generateJWT(userID string) (string, error) {
	now := time.Now()

	claims := helpers.JWTClaims{
		UserID: userID,
		RegisteredClaims: jwt.RegisteredClaims{
			ExpiresAt: jwt.NewNumericDate(now.Add(time.Minute * jwtExpiryInMinutes)),
			IssuedAt:  jwt.NewNumericDate(now),
			NotBefore: jwt.NewNumericDate(now),
		},
	}

	token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	signedToken, err := token.SignedString([]byte(os.Getenv("JWT_SECRET")))
	if err != nil {
		return "", fmt.Errorf("failed to sign token: %w", err)
	}

	parts := strings.Split(signedToken, ".")
	if len(parts) != 3 {
		return "", fmt.Errorf("generated token has %d parts, expected 3", len(parts))
	}

	return signedToken, nil
}
