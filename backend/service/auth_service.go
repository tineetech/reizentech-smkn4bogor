package service

import (
	"context"
	"fmt"
	"os"
	"strings"
	"time"

	"github.com/golang-jwt/jwt/v5" // ganti versi/driver sesuai kebutuhan
	dto "github.com/muhammadridwansurya/api-go/dto"
	"github.com/muhammadridwansurya/api-go/helpers"
	"github.com/muhammadridwansurya/api-go/model"
	repo "github.com/muhammadridwansurya/api-go/repository"
)

// -------------------------------------------------------------------
// Konfigurasi JWT sederhana.
// Idealnya secret & expiry di‑inject via env / config.
// -------------------------------------------------------------------

const (
	jwtExpiryInMinutes = 60 // ✅ bisa jadi const atau var
)

// -------------------------------------------------------------------
// Interface Service
// -------------------------------------------------------------------

type AuthServiceInterface interface {
	Register(ctx context.Context, req *dto.RegisterRequest) (*model.User, error)
	Login(ctx context.Context, req *dto.LoginRequest) (*model.User, string, error)
	Logout(ctx context.Context, userID string) error
	GetProfile(ctx context.Context, userID string) (*model.User, error)
}

// -------------------------------------------------------------------
// Implementasi Service
// -------------------------------------------------------------------

type authService struct {
	repo repo.AuthRepositoryInterface
}

func InitAuthService(r repo.AuthRepositoryInterface) AuthServiceInterface {
	return &authService{repo: r}
}

// -------------------------------------------------------------------
// Register
// -------------------------------------------------------------------
func (s *authService) Register(ctx context.Context, req *dto.RegisterRequest) (*model.User, error) {
	return s.repo.Register(ctx, req)
}

// -------------------------------------------------------------------
// Login
// -------------------------------------------------------------------
func (s *authService) Login(ctx context.Context, req *dto.LoginRequest) (*model.User, string, error) {
	user, err := s.repo.Login(ctx, req)
	if err != nil {
		return nil, "", err
	}

	tokenStr, err := generateJWT(user.ID)
	if err != nil {
		return nil, "", fmt.Errorf("gagal membuat token: %w", err)
	}

	// Simpan token sebagai remember_token (opsional). Tidak menggagalkan login.
	if err := s.repo.UpdateRememberToken(ctx, user.ID, &tokenStr); err != nil {
		// TODO: log error jika diperlukan
	}

	return user, tokenStr, nil
}

// -------------------------------------------------------------------
// Logout  →  kosongkan remember_token
// -------------------------------------------------------------------
func (s *authService) Logout(ctx context.Context, userID string) error {
	return s.repo.Logout(ctx, userID)
}

// -------------------------------------------------------------------
// Get profile (by JWT claims → userID)
// -------------------------------------------------------------------
func (s *authService) GetProfile(ctx context.Context, userID string) (*model.User, error) {
	if userID == "" {
		return nil, fmt.Errorf("user ID tidak valid")
	}
	return s.repo.GetUserByID(ctx, userID)
}

// -------------------------------------------------------------------
// Helper: JWT generator
// -------------------------------------------------------------------

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

	// Verify the unsigned token looks correct
	unsignedToken, _ := token.SigningString()
	fmt.Printf("Unsigned token parts: %v\n", strings.Split(unsignedToken, "."))

	signedToken, err := token.SignedString([]byte(os.Getenv("JWT_SECRET")))
	if err != nil {
		return "", fmt.Errorf("failed to sign token: %w", err)
	}

	// Verify the signed token structure
	parts := strings.Split(signedToken, ".")
	if len(parts) != 3 {
		return "", fmt.Errorf("generated token has %d parts, expected 3", len(parts))
	}

	fmt.Printf("Generated token: %s\n", signedToken)
	return signedToken, nil
}
