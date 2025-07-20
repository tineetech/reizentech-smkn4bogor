// file: model/user_biodata.go
package model

import (
	"time"
)

const TableUserBiodata = "user_biodata"

type UserBiodata struct {
	ID          string    `json:"id"              db:"id"`
	UserID      string    `json:"user_id"         db:"user_id"`
	UserCode    string    `json:"user_code"       db:"user_code"`
	Name        string    `json:"name"            db:"name"`
	Sex         string    `json:"sex"             db:"sex"` // 'L' atau 'P'
	DateOfBirth time.Time `json:"date_of_birth"   db:"date_of_birth"`
	Status      bool      `json:"status"          db:"status"`
	CreatedAt   time.Time `json:"created_at"      db:"created_at"`
	UpdatedAt   time.Time `json:"updated_at"      db:"updated_at"`
}
