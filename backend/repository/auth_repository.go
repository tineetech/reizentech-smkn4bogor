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

type AuthRepositoryInterface interface {
	Register(ctx context.Context, data *dto.RegisterRequest) (*model.User, error)
	Login(ctx context.Context, data *dto.LoginRequest) (*model.User, error)
	Logout(ctx context.Context, userID string) error
	VerifyEmail(ctx context.Context, req *dto.VerifyEmailRequest) error
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
	userID := generateRandomString(26)

	hashedPwd, err := bcrypt.GenerateFromPassword([]byte(data.Password), bcrypt.DefaultCost)
	if err != nil {
		return nil, err
	}

	tx, err := r.db.BeginTx(ctx, nil)
	if err != nil {
		return nil, err
	}

	defer func() {
		if err != nil {
			tx.Rollback()
		} else {
			tx.Commit()
		}
	}()
	queryUser := `
        INSERT INTO users (id, username, email, password, status, created_at, updated_at)
        VALUES ($1, $2, $3, $4, 'active', NOW(), NOW())
        RETURNING id, username, email, status, created_at, updated_at
    `

	row := tx.QueryRowContext(ctx, queryUser, userID, data.Username, data.Email, string(hashedPwd))

	var user model.User
	err = row.Scan(
		&user.ID,
		&user.Username,
		&user.Email,
		&user.Status,
		&user.CreatedAt,
		&user.UpdatedAt,
	)
	if err != nil {
		return nil, err
	}

	defaultLevelID := "01JZP9JEY9A4PZX3VSTKFEPAQD"
	userRoleID := generateRandomString(26)

	_, err = tx.ExecContext(ctx, `
        INSERT INTO user_role (id, user_id, user_level_id, status, created_at, updated_at)
        VALUES ($1, $2, $3, $4, NOW(), NOW())
    `, userRoleID, userID, defaultLevelID, true)

	if err != nil {
		return nil, err
	}

	return &user, nil
}

func generateRandomString(length int) string {
	const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
	b := make([]byte, length)
	for i := range b {
		b[i] = charset[rand.Intn(len(charset))]
	}
	return string(b)
}
func (r *authRepository) Login(ctx context.Context, data *dto.LoginRequest) (*model.User, error) {
	// Ambil user dari database, termasuk email_verified_at
	query := fmt.Sprintf(`
        SELECT id, username, email, email_verified_at, password, remember_token,
               status, created_at, updated_at
        FROM %s
        WHERE (username = $1 OR email = $1)
        LIMIT 1
    `, model.TableUser)

	var (
		user             model.User
		hashedPwd        string
		rememberTokenPtr *string
		emailVerifiedAt  *time.Time
	)

	err := r.db.QueryRowContext(ctx, query, data.Email).Scan(
		&user.ID,
		&user.Username,
		&user.Email,
		&emailVerifiedAt,
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

	user.EmailVerifiedAt = emailVerifiedAt
	user.Password = ""
	user.RememberToken = rememberTokenPtr

	if user.Status != model.UserStatusActive {
		return nil, fmt.Errorf("akun tidak aktif")
	}

	if user.EmailVerifiedAt == nil {
		return nil, fmt.Errorf("email belum diverifikasi")
	}

	if err := bcrypt.CompareHashAndPassword([]byte(hashedPwd), []byte(data.Password)); err != nil {
		return nil, fmt.Errorf("password salah")
	}

	return &user, nil
}

func (r *authRepository) Logout(ctx context.Context, userID string) error {
	return r.UpdateRememberToken(ctx, userID, nil)
}

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

func (r *authRepository) VerifyEmail(ctx context.Context, req *dto.VerifyEmailRequest) error {
	query := fmt.Sprintf(`
		UPDATE %s
		SET email_verified_at = $1,
			updated_at = $1
		WHERE email = $2
	`, model.TableUser)

	result, err := r.db.ExecContext(ctx, query, time.Now(), req.Token)
	if err != nil {
		return fmt.Errorf("gagal memverifikasi email: %w", err)
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("gagal membaca hasil verifikasi email: %w", err)
	}

	if rowsAffected == 0 {
		return fmt.Errorf("email tidak ditemukan atau sudah diverifikasi")
	}

	return nil
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
