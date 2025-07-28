package service_v1

import (
	"context"
	"fmt"

	dto_v1 "github.com/muhammadridwansurya/api-go/dto/v1"
	"github.com/muhammadridwansurya/api-go/model"
	repository_v1 "github.com/muhammadridwansurya/api-go/repository/v1"
)

// service template logika nya aplikasi yg menampilkan berupa pesan

type KelasServiceInterface interface {
	GetJumlahKelas(ctx context.Context) (*model.JmlKelas, error)
	GetDaftarKelas(ctx context.Context) ([]model.Kelas, error)
	GetKelasById(ctx context.Context, id uint32) (*model.Kelas, error)
	CreateKelas(ctx context.Context, req *dto_v1.KelasRequest) (*model.Kelas, error)
	UpdateKelas(ctx context.Context, id uint32, req *dto_v1.KelasRequest) (*model.Kelas, error)
	DeleteKelas(ctx context.Context, id uint32) (*model.Kelas, error)
}

type kelasService struct {
	repo repository_v1.KelasRepositoryInterface
}

func InitKelasService(repo repository_v1.KelasRepositoryInterface) KelasServiceInterface {
	return &kelasService{repo: repo}
}

func (s *kelasService) GetJumlahKelas(ctx context.Context) (*model.JmlKelas, error) {
	return s.repo.GetJmlKelas(ctx)
}

func (s *kelasService) GetDaftarKelas(ctx context.Context) ([]model.Kelas, error) {
	return s.repo.GetKelas(ctx)
}

func (s *kelasService) GetKelasById(ctx context.Context, id uint32) (*model.Kelas, error) {
	if id == 0 {
		return nil, fmt.Errorf("ID kelas tidak ditemukan")
	}
	return s.repo.GetKelasById(ctx, id)
}

func (s *kelasService) CreateKelas(ctx context.Context, req *dto_v1.KelasRequest) (*model.Kelas, error) {
	return s.repo.InsertKelas(ctx, req)
}

func (s *kelasService) UpdateKelas(ctx context.Context, id uint32, req *dto_v1.KelasRequest) (*model.Kelas, error) {
	// Contoh Validasi bisa ditambahkan sesuai kebutuhan
	if id == 0 {
		return nil, fmt.Errorf("ID tidak valid")
	}

	return s.repo.UpdateKelas(ctx, id, req)
}

func (s *kelasService) DeleteKelas(ctx context.Context, id uint32) (*model.Kelas, error) {
	return s.repo.DeleteKelas(ctx, id)
}
