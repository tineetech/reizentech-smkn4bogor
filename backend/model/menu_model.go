// model/menu.go – Definisi model sederhana tanpa repository/ORM
// Ikuti gaya contoh User yang Anda lampirkan (hanya konstanta, enum, struct, dan TableName)

package model

import "time"

// --------------------------------------------------
//
//  Nama tabel (hindari hard‑code di query)
//
// --------------------------------------------------
const TableMenu = "menu"

// --------------------------------------------------
//
//  Enum menu_type (sesuai CHECK CONSTRAINT)
//
// --------------------------------------------------
type MenuType string

const (
	MenuTypeMain MenuType = "main"
	MenuTypeSub  MenuType = "sub"
)

// --------------------------------------------------
//
//  Model utama: Menu
//
// --------------------------------------------------
// Kolom nullable → gunakan pointer (*string, *MenuType, *time.Time) bila ada.
// Dalam tabel "menu", semua kolom non‑nullable kecuali mungkin "updated_at" (namun di DB Anda not null).
// Password tidak relevan di sini.
//
// Tag `json` agar mudah di‑marshal ke/ dari JSON.
// Tag `db` berguna untuk sqlx (boleh dihapus bila pakai database/sql murni).
// Tag `gorm` disertakan kalau suatu hari pindah ke GORM.
//
type Menu struct {
	ID        int64     `json:"id"         db:"id"         `
	MenuCode  string    `json:"menu_code"  db:"menu_code"  `
	MenuName  string    `json:"menu_name"  db:"menu_name"  `
	MenuType  MenuType  `json:"menu_type"  db:"menu_type"  `
	Status    bool      `json:"status"     db:"status"     `
	CreatedAt time.Time `json:"created_at" db:"created_at" `
	UpdatedAt time.Time `json:"updated_at" db:"updated_at" `
}
