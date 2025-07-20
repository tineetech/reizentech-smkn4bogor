// file: model/app_contact_me.go
package model

import "time"

const TableAppContactMe = "app_contact_me"

type AppContactMe struct {
	ID        int64     `json:"id"         db:"id"`         // Primary key
	AppID     int64     `json:"app_id"     db:"app_id"`     // Foreign key ke tabel app
	Name      string    `json:"name"       db:"name"`       // Nama kontak (misal: WhatsApp, Instagram, dll)
	Icon      *string   `json:"icon"       db:"icon"`       // Icon optional (nullable)
	URL       *string   `json:"url"        db:"url"`        // URL optional (nullable)
	Order     int16     `json:"order"      db:"order"`      // Urutan tampil
	Status    bool      `json:"status"     db:"status"`     // Aktif / tidak
	CreatedAt time.Time `json:"created_at" db:"created_at"` // Timestamp dibuat
	UpdatedAt time.Time `json:"updated_at" db:"updated_at"` // Timestamp update
}
