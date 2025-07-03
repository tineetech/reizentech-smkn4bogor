package dto

type KelasRequest struct {
	ID        string `json:"id,omitempty" validate:"required"`
	NamaKelas string `json:"nama_kelas" validate:"required,min=6,max=255,unique=nama_kelas.kelas"`
	Status    string `json:"status" validate:"required,in=true.false"`
}
