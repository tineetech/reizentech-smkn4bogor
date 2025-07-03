package model

import "time"

const (
	TableKelas = `kelas`
)

// intinya model ini tempat hasil nilai dari query

type JmlKelas struct {
	Jml uint16 `json:"jml_kelas"` // menampung jml kelas
}

type Kelas struct { // Struktur kolom tabel kelas
	ID        uint32     `json:"id"`
	NamaKelas string     `json:"nama_kelas"`
	Status    string     `json:"status"`
	CreatedAt *time.Time `json:"created_at,omitempty"`
	UpdatedAt *time.Time `json:"updated_at,omitempty"`
}
