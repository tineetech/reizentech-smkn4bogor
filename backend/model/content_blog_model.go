// file: model/content_blog.go
package model

import "time"

const TableContentBlog = "content_blog"

type ContentBlog struct {
	ID                     string    `json:"id"                            db:"id"`
	UserRoleID             string    `json:"user_role_id"                  db:"user_role_id"`
	LastUpdateByUserRoleID string    `json:"last_update_by_user_role_id"  db:"last_update_by_user_role_id"`
	MenuRoleID             *int64    `json:"menu_role_id,omitempty"        db:"menu_role_id"`
	Slug                   string    `json:"slug"                          db:"slug"`
	Title                  string    `json:"title"                         db:"title"`
	Description            *string   `json:"description,omitempty"         db:"description"`
	Content                string    `json:"content"                       db:"content"`
	Tipe                   string    `json:"tipe"                          db:"tipe"`
	Status                 bool      `json:"status"                        db:"status"`
	CreatedAt              time.Time `json:"created_at"                    db:"created_at"`
	UpdatedAt              time.Time `json:"updated_at"                    db:"updated_at"`
}
