// file: model/profile_content_blog.go
package model

import "time"

const TableProfileContentBlog = "profile_content_blog"

type ProfileContentBlog struct {
	ID         int64     `json:"id"              db:"id"`           // PRIMARY KEY
	MenuRoleID int64     `json:"menu_role_id"    db:"menu_role_id"` // FK ke menu_role(id)
	Slug       string    `json:"slug"            db:"slug"`         // UNIQUE
	Name       string    `json:"name"            db:"name"`
	Logo       *string   `json:"logo,omitempty"  db:"logo"`    // Nullable
	Content    string    `json:"content"         db:"content"` // Text
	Tipe       string    `json:"tipe"            db:"tipe"`    // Enum: 'major' atau 'menu_code'
	Order      int16     `json:"order"           db:"order"`   // smallint
	Status     bool      `json:"status"          db:"status"`
	CreatedAt  time.Time `json:"created_at"      db:"created_at"`
	UpdatedAt  time.Time `json:"updated_at"      db:"updated_at"`
}
