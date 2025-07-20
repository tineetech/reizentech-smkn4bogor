// file: model/app.go
package model

import "time"

const TableApp = "app"

type App struct {
	ID        int64     `json:"id"         db:"id"`         // PRIMARY KEY, bigserial
	AppName   string    `json:"app_name"   db:"app_name"`   // UNIQUE
	Desc      string    `json:"desc"       db:"desc"`       // Deskripsi aplikasi
	Status    bool      `json:"status"     db:"status"`     // Aktif / Tidak aktif
	CreatedAt time.Time `json:"created_at" db:"created_at"` // Timestamp saat dibuat
	UpdatedAt time.Time `json:"updated_at" db:"updated_at"` // Timestamp saat update
}
