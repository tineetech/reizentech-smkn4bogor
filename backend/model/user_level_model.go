package model

import (
	"time"
)

const TableUserLevel = "user_level"

type UserLevel struct {
	ID            string    `json:"id"               db:"id"`
	BiodataRefID  int64     `json:"biodata_ref_id"   db:"biodata_ref_id"`
	AppID         int64     `json:"app_id"           db:"app_id"`
	UserLevelName string    `json:"user_level_name"  db:"user_level_name"`
	Status        bool      `json:"status"           db:"status"`
	CreatedAt     time.Time `json:"created_at"       db:"created_at"`
	UpdatedAt     time.Time `json:"updated_at"       db:"updated_at"`
}
