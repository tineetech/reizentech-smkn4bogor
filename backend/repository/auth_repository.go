package repository

import (
	"context"
	"database/sql"
	"errors"
	"fmt"
	"math/rand"
	"time"

	"github.com/muhammadridwansurya/api-go/dto"
	"github.com/muhammadridwansurya/api-go/model"
	"golang.org/x/crypto/bcrypt"
)

// -------------------------------------------------------------------
// Interface
// -------------------------------------------------------------------

type AuthRepositoryInterface interface {
	Register(ctx context.Context, data *dto.RegisterRequest) (*model.User, error)
	Login(ctx context.Context, data *dto.LoginRequest) (*model.User, error)
	Logout(ctx context.Context, userID string) error

	GetUserByID(ctx context.Context, id string) (*model.User, error)
	GetUserByUsernameOrEmail(ctx context.Context, identifier string) (*model.User, error)
	UpdateRememberToken(ctx context.Context, userID string, token *string) error
}

type authRepository struct {
	db *sql.DB
}

func InitAuthRepository(db *sql.DB) AuthRepositoryInterface {
	return &authRepository{db: db}
}

func (r *authRepository) Register(ctx context.Context, data *dto.RegisterRequest) (*model.User, error) {
	id := generateRandomString(26)

	hashedPwd, err := bcrypt.GenerateFromPassword([]byte(data.Password), bcrypt.DefaultCost)
	if err != nil {
		return nil, err
	}

	query := fmt.Sprintf(`
        INSERT INTO %s 
        (id, username, email, phone_number, password, status, created_at, updated_at)
        VALUES ($1, $2, $3, $4, $5, $6, NOW(), NOW())
        RETURNING id, username, email, phone_number, status, created_at, updated_at
    `, model.TableUser)

	row := r.db.QueryRowContext(ctx, query,
		id,
		data.Username,
		data.Email,
		data.PhoneNumber,
		string(hashedPwd),
		model.UserStatusActive,
	)

	var user model.User
	if err := row.Scan(
		&user.ID,
		&user.Username,
		&user.Email,
		&user.PhoneNumber,
		&user.Status,
		&user.CreatedAt,
		&user.UpdatedAt,
	); err != nil {
		return nil, err
	}

	return &user, nil
}

// Helper function untuk generate random string
func generateRandomString(length int) string {
	const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
	b := make([]byte, length)
	for i := range b {
		b[i] = charset[rand.Intn(len(charset))]
	}
	return string(b)
}
func (r *authRepository) Login(ctx context.Context, data *dto.LoginRequest) (*model.User, error) {
	// Ambil user (dengan hash password) terlebih dahulu
	query := fmt.Sprintf(`
        SELECT id, username, email, phone_number, password, remember_token,
               status, created_at, updated_at
        FROM %s
        WHERE (username = $1 OR email = $1)
        LIMIT 1
    `, model.TableUser)

	var (
		user             model.User
		hashedPwd        string
		rememberTokenPtr *string
	)
	err := r.db.QueryRowContext(ctx, query, data.Email).Scan(
		&user.ID,
		&user.Username,
		&user.Email,
		&user.PhoneNumber,
		&hashedPwd,
		&rememberTokenPtr,
		&user.Status,
		&user.CreatedAt,
		&user.UpdatedAt,
	)
	if err != nil {
		if errors.Is(err, sql.ErrNoRows) {
			return nil, fmt.Errorf("akun tidak ditemukan")
		}
		return nil, err
	}

	if user.Status != model.UserStatusActive {
		return nil, fmt.Errorf("akun tidak aktif")
	}

	if err := bcrypt.CompareHashAndPassword([]byte(hashedPwd), []byte(data.Password)); err != nil {
		return nil, fmt.Errorf("password salah")
	}

	user.Password = ""
	user.RememberToken = rememberTokenPtr

	return &user, nil
}

// -------------------------------------------------------------------
// Logout  →  kosongkan remember_token
// -------------------------------------------------------------------

func (r *authRepository) Logout(ctx context.Context, userID string) error {
	return r.UpdateRememberToken(ctx, userID, nil)
}

// -------------------------------------------------------------------
// Helper methods
// -------------------------------------------------------------------

func (r *authRepository) GetUserByID(ctx context.Context, id string) (*model.User, error) {
	query := fmt.Sprintf(`
        SELECT id, username, email, phone_number, status,
               created_at, updated_at
        FROM %s
        WHERE id = $1
    `, model.TableUser)

	user := &model.User{}
	err := r.db.QueryRowContext(ctx, query, id).Scan(
		&user.ID,
		&user.Username,
		&user.Email,
		&user.PhoneNumber,
		&user.Status,
		&user.CreatedAt,
		&user.UpdatedAt,
	)
	if err != nil {
		if errors.Is(err, sql.ErrNoRows) {
			return nil, fmt.Errorf("ID user tidak ditemukan")
		}
		return nil, err
	}
	return user, nil
}

func (r *authRepository) GetUserByUsernameOrEmail(ctx context.Context, identifier string) (*model.User, error) {
	query := fmt.Sprintf(`
        SELECT id, username, email, phone_number, status,
               created_at, updated_at
        FROM %s
        WHERE username = $1 OR email = $1
        LIMIT 1
    `, model.TableUser)

	user := &model.User{}
	err := r.db.QueryRowContext(ctx, query, identifier).Scan(
		&user.ID,
		&user.Username,
		&user.Email,
		&user.PhoneNumber,
		&user.Status,
		&user.CreatedAt,
		&user.UpdatedAt,
	)
	if err != nil {
		if errors.Is(err, sql.ErrNoRows) {
			return nil, fmt.Errorf("user tidak ditemukan")
		}
		return nil, err
	}
	return user, nil
}

// UpdateRememberToken – dipakai untuk fitur "remember me" di login.
func (r *authRepository) UpdateRememberToken(ctx context.Context, userID string, token *string) error {
	tx, err := r.db.BeginTx(ctx, nil)
	if err != nil {
		return err
	}
	defer tx.Rollback()

	query := fmt.Sprintf(`
        UPDATE %s
        SET remember_token = $1,
            updated_at     = $2
        WHERE id = $3
    `, model.TableUser)

	if _, err := tx.ExecContext(ctx, query, token, time.Now(), userID); err != nil {
		return err
	}

	return tx.Commit()
}
