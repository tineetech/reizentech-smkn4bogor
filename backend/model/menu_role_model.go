// file: model/menu_role.go
package model

import "time"

const TableMenuRole = "menu_role"

type MenuRole struct {
	ID             int64     `json:"id"                 db:"id"`
	MenuID         int64     `json:"menu_id"            db:"menu_id"`
	UserLevelID    string    `json:"user_level_id"      db:"user_level_id"`
	ParentMenuCode *string   `json:"parent_menu_code"   db:"parent_menu_code"`
	MenuIcon       *string   `json:"menu_icon"          db:"menu_icon"`
	MenuURL        *string   `json:"menu_url"           db:"menu_url"`
	RoleView       bool      `json:"role_view"          db:"role_view"`
	RoleCreate     bool      `json:"role_create"        db:"role_create"`
	RoleUpdate     bool      `json:"role_update"        db:"role_update"`
	RoleDelete     bool      `json:"role_delete"        db:"role_delete"`
	RoleOrder      int16     `json:"role_order"         db:"role_order"`
	Status         bool      `json:"status"             db:"status"`
	CreatedAt      time.Time `json:"created_at"         db:"created_at"`
	UpdatedAt      time.Time `json:"updated_at"         db:"updated_at"`
}
