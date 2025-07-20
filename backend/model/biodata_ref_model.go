// file: model/biodata_ref.go
package model

import "time"

const TableBiodataRef = "biodata_ref"

type BiodataRef struct {
	ID             int64     `json:"id"                  db:"id"`               // PRIMARY KEY, bigserial
	BiodataRefName string    `json:"biodata_ref_name"    db:"biodata_ref_name"` // UNIQUE
	Status         bool      `json:"status"              db:"status"`           // Aktif / Tidak aktif
	CreatedAt      time.Time `json:"created_at"          db:"created_at"`       // Default: CURRENT_TIMESTAMP
	UpdatedAt      time.Time `json:"updated_at"          db:"updated_at"`       // Ditrigger otomatis
}
