package repository

import (
	"context"
	"database/sql"
	"fmt"

	"github.com/muhammadridwansurya/api-go/dto"
	"github.com/muhammadridwansurya/api-go/model"
)

// -------------------------------------------------------------------
// Interface repository Users
// -------------------------------------------------------------------
type UserRepositoryInterface interface {
	GetDaftarUsers(ctx context.Context) ([]model.User, error)
	GetUserByID(ctx context.Context, userID string) (*model.User, error)
	InsertUser(ctx context.Context, data *dto.UserRequest) (*model.User, error)
	UpdateUser(ctx context.Context, userID string, data *dto.UserRequest) (*model.User, error)
	DeleteUser(ctx context.Context, userID string) (*model.User, error)
}

// -------------------------------------------------------------------
// Implementasi repository
// -------------------------------------------------------------------
type userRepository struct{ db *sql.DB }

func InitUserRepository(db *sql.DB) UserRepositoryInterface {
	return &userRepository{db: db}
}

// -------------------------------------------------------------------
// Ambil semua user (tanpa password & remember_token)
// -------------------------------------------------------------------
func (r *userRepository) GetDaftarUsers(ctx context.Context) ([]model.User, error) {
	var users []model.User

	q := fmt.Sprintf(`
		SELECT id, username, email, email_verified_at,
		       phone_number, phone_number_verified_at,
		       status, created_at, updated_at
		FROM %s ORDER BY created_at DESC
	`, model.TableUser)

	rows, err := r.db.QueryContext(ctx, q)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	for rows.Next() {
		u, err := scanUserRow(rows)
		if err != nil {
			return nil, err
		}
		users = append(users, *u)
	}
	if err := rows.Err(); err != nil {
		return nil, err
	}
	return users, nil
}

// -------------------------------------------------------------------
// Ambil user by ID
// -------------------------------------------------------------------
func (r *userRepository) GetUserByID(ctx context.Context, id string) (*model.User, error) {
	q := fmt.Sprintf(`
		SELECT id, username, email, email_verified_at,
		       phone_number, phone_number_verified_at,
		       status, created_at, updated_at
		FROM %s WHERE id = $1 LIMIT 1
	`, model.TableUser)

	row := r.db.QueryRowContext(ctx, q, id)
	u, err := scanUserRow(row)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, fmt.Errorf("ID user tidak ditemukan")
		}
		return nil, err
	}
	return u, nil
}

// -------------------------------------------------------------------
// Insert user
// -------------------------------------------------------------------
func (r *userRepository) InsertUser(ctx context.Context, d *dto.UserRequest) (*model.User, error) {
	tx, err := r.db.BeginTx(ctx, nil)
	if err != nil {
		return nil, err
	}
	defer tx.Rollback()

	q := fmt.Sprintf(`
		INSERT INTO %s
		    (username, email, phone_number, password,
		     status, created_at, updated_at)
		VALUES ($1, $2, $3, $4, $5, NOW(), NOW())
		RETURNING id, username, email, email_verified_at,
		          phone_number, phone_number_verified_at,
		          status, created_at, updated_at
	`, model.TableUser)

	row := tx.QueryRowContext(ctx, q,
		d.Username,
		sql.NullString{String: d.Email, Valid: d.Email != ""},
		sql.NullString{String: d.PhoneNumber, Valid: d.PhoneNumber != ""},
		d.PasswordHash,
		d.Status,
	)
	u, err := scanUserRow(row)
	if err != nil {
		return nil, err
	}

	if err := tx.Commit(); err != nil {
		return nil, err
	}
	return u, nil
}

// -------------------------------------------------------------------
// Update user (support reset field ke NULL)
// -------------------------------------------------------------------
func (r *userRepository) UpdateUser(ctx context.Context, id string, d *dto.UserRequest) (*model.User, error) {
	tx, err := r.db.BeginTx(ctx, nil)
	if err != nil {
		return nil, err
	}
	defer tx.Rollback()

	// Build dynamic query (password optional)
	base := fmt.Sprintf(`
		UPDATE %s SET
		  username     = $1,
		  email        = $2,
		  phone_number = $3,
		  status       = $4,
		  updated_at   = NOW()`, model.TableUser)
	params := []any{
		d.Username,
		sql.NullString{String: d.Email, Valid: d.Email != ""},
		sql.NullString{String: d.PhoneNumber, Valid: d.PhoneNumber != ""},
		d.Status,
	}
	idx := 5
	if d.PasswordHash != "" {
		base += fmt.Sprintf(", password = $%d", idx)
		params = append(params, d.PasswordHash)
		idx++
	}
	base += fmt.Sprintf(" WHERE id = $%d", idx)
	params = append(params, id)
	base += ` RETURNING id, username, email, email_verified_at,
	                 phone_number, phone_number_verified_at,
	                 status, created_at, updated_at`

	row := tx.QueryRowContext(ctx, base, params...)
	u, err := scanUserRow(row)
	if err != nil {
		return nil, err
	}
	return u, tx.Commit()
}

// -------------------------------------------------------------------
// Delete user
// -------------------------------------------------------------------
func (r *userRepository) DeleteUser(ctx context.Context, id string) (*model.User, error) {
	tx, err := r.db.BeginTx(ctx, nil)
	if err != nil {
		return nil, err
	}
	defer tx.Rollback()

	u, err := r.GetUserByID(ctx, id)
	if err != nil {
		return nil, err
	}
	q := fmt.Sprintf(`DELETE FROM %s WHERE id = $1`, model.TableUser)
	if _, err := tx.ExecContext(ctx, q, id); err != nil {
		return nil, err
	}
	return u, tx.Commit()
}

// -------------------------------------------------------------------
// Helper scan (bekerja utk sql.Row & sql.Rows)
// -------------------------------------------------------------------
type scanner interface {
	Scan(dest ...any) error
}

func scanUserRow(s scanner) (*model.User, error) {
	var (
		u        model.User
		email    sql.NullString
		emailVer sql.NullTime
		phone    sql.NullString
		phoneVer sql.NullTime
	)
	if err := s.Scan(
		&u.ID, &u.Username,
		&email, &emailVer,
		&phone, &phoneVer,
		&u.Status, &u.CreatedAt, &u.UpdatedAt,
	); err != nil {
		return nil, err
	}
	if email.Valid {
		u.Email = &email.String
	}
	if emailVer.Valid {
		u.EmailVerifiedAt = &emailVer.Time
	}
	if phone.Valid {
		u.PhoneNumber = &phone.String
	}
	if phoneVer.Valid {
		u.PhoneNumberVerifiedAt = &phoneVer.Time
	}
	return &u, nil
}
