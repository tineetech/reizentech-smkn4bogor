package model

import "time"

const TableUser = "users"

type UserStatus string

const (
	UserStatusActive    UserStatus = "active"
	UserStatusNonActive UserStatus = "non-activate"
	UserStatusBanned    UserStatus = "banned"
)

type User struct {
	ID                    string     `json:"id"                           db:"id"`
	Username              string     `json:"username"                     db:"username"`
	Email                 *string    `json:"email,omitempty"              db:"email"`
	EmailVerifiedAt       *time.Time `json:"email_verified_at,omitempty"  db:"email_verified_at"`
	PhoneNumber           *string    `json:"phone_number,omitempty"       db:"phone_number"`
	PhoneNumberVerifiedAt *time.Time `json:"phone_number_verified_at,omitempty" db:"phone_number_verified_at"`
	Password              string     `json:"-"                            db:"password"`
	RememberToken         *string    `json:"-"                            db:"remember_token"`
	Status                UserStatus `json:"status"                       db:"status"`
	CreatedAt             time.Time  `json:"created_at"                   db:"created_at"`
	UpdatedAt             time.Time  `json:"updated_at"                   db:"updated_at"`
}
