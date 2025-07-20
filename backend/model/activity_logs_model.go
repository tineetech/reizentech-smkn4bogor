// file: model/activity_log.go
package model

import (
	"net"
	"time"
)

const TableActivityLog = "activity_logs"

type ActivityLog struct {
	ID           string    `json:"id"            db:"id"`                  // ULID sebagai primary key
	UserRoleID   string    `json:"user_role_id"  db:"user_role_id"`        // Foreign key ke user_role
	Activity     string    `json:"activity"      db:"activity"`            // Nama aktivitas/log (misal: login, update profile)
	Description  *string   `json:"description,omitempty" db:"description"` // Deskripsi tambahan (opsional)
	IPAddress    *net.IP   `json:"ip_address,omitempty" db:"ip_address"`   // Alamat IP (opsional)
	UserAgent    *string   `json:"user_agent,omitempty" db:"user_agent"`   // User agent browser (opsional)
	ActivityTime time.Time `json:"activity_time" db:"activity_time"`       // Waktu aktivitas terjadi (default: now)
}
