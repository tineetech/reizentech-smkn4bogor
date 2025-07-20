// file: model/app_config_relation_blog.go
package model

import "time"

const TableAppConfigRelationBlog = "app_config_relation_blog"

type AppConfigRelationBlog struct {
	ID        int64     `json:"id"         db:"id"`         // Primary key
	Image     string    `json:"image"      db:"image"`      // Nama file gambar
	URL       *string   `json:"url"        db:"url"`        // Optional URL tujuan
	Order     int16     `json:"order"      db:"order"`      // Urutan tampil
	Status    bool      `json:"status"     db:"status"`     // Status aktif / tidak
	CreatedAt time.Time `json:"created_at" db:"created_at"` // Timestamp dibuat
	UpdatedAt time.Time `json:"updated_at" db:"updated_at"` // Timestamp update terakhir
}
