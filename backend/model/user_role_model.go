package model

import "time"

const TableUserRole = "user_role"

type UserRole struct {
	ID          string    `json:"id"              db:"id"`
	UserID      string    `json:"user_id"         db:"user_id"`
	UserLevelID string    `json:"user_level_id"   db:"user_level_id"`
	Status      bool      `json:"status"          db:"status"`
	CreatedAt   time.Time `json:"created_at"      db:"created_at"`
	UpdatedAt   time.Time `json:"updated_at"      db:"updated_at"`
}
