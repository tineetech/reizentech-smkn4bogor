// file: model/tag_blog.go
package model

import "time"

const TableTagBlog = "tag_blog"

type TagBlog struct {
	ID        int64     `json:"id"         db:"id"`         // PRIMARY KEY
	Name      string    `json:"name"       db:"name"`       // UNIQUE
	Status    bool      `json:"status"     db:"status"`     // NOT NULL
	CreatedAt time.Time `json:"created_at" db:"created_at"` // DEFAULT now()
	UpdatedAt time.Time `json:"updated_at" db:"updated_at"` // DEFAULT now(), updated via trigger
}
