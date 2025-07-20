// file: model/app_config_blog.go
package model

import "time"

const TableAppConfigBlog = "app_config_blog"

type AppConfigBlog struct {
	ID                      int64     `json:"id"                          db:"id"`                        // Primary key
	Name                    string    `json:"name"                        db:"name"`                      // Nama aplikasi/blog
	Video                   *string   `json:"video,omitempty"             db:"video"`                     // URL video (opsional)
	ShortSentences          *string   `json:"short_sentences,omitempty"  db:"short_sentences"`            // Kalimat pendek untuk deskripsi
	LogoHeader              *string   `json:"logo_header,omitempty"       db:"logo_header"`               // Logo bagian header (opsional)
	LogoFooter              *string   `json:"logo_footer,omitempty"       db:"logo_footer"`               // Logo bagian footer (opsional)
	Address                 string    `json:"address"                     db:"address"`                   // Alamat fisik/toko/perusahaan
	LocationIframe          *string   `json:"location_iframe,omitempty"  db:"location_iframe"`            // Embed Google Maps iframe (opsional)
	ShowProfileContentMajor bool      `json:"show_profile_content_major" db:"show_profile_content_major"` // Tampilkan section major profile?
	ShowJumbotron           bool      `json:"show_jumbotron"             db:"show_jumbotron"`             // Tampilkan jumbotron?
	ShowRelation            bool      `json:"show_relation"              db:"show_relation"`              // Tampilkan relasi section?
	Status                  string    `json:"status"                      db:"status"`                    // "active" / "maintenance" / "non-active"
	CreatedAt               time.Time `json:"created_at"                  db:"created_at"`                // Timestamp saat dibuat
	UpdatedAt               time.Time `json:"updated_at"                  db:"updated_at"`                // Timestamp saat diubah
}
