// file: model/app_config_jumbotron.go
package model

import "time"

const TableAppConfigJumbotron = "app_config_jumbotron"

type AppConfigJumbotron struct {
	ID                int64      `json:"id"                    db:"id"`
	AppID             int64      `json:"app_id"                db:"app_id"`
	Banner            string     `json:"banner"                db:"banner"`
	URL               *string    `json:"url,omitempty"         db:"url"`
	Tipe              string     `json:"tipe"                  db:"tipe"`
	Show              string     `json:"show"                  db:"show"`
	ShowStartDatetime *time.Time `json:"show_start_datetime"   db:"show_start_datetime"`
	ShowEndDatetime   *time.Time `json:"show_end_datetime"     db:"show_end_datetime"`
	Order             int16      `json:"order"                 db:"order"`
	Status            bool       `json:"status"                db:"status"`
	CreatedAt         time.Time  `json:"created_at"            db:"created_at"`
	UpdatedAt         time.Time  `json:"updated_at"            db:"updated_at"`
}
