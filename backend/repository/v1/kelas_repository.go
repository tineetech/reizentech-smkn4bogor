package repository_v1

import (
	"context"
	"database/sql"
	"fmt"

	dto_v1 "github.com/muhammadridwansurya/api-go/dto/v1"
	"github.com/muhammadridwansurya/api-go/model"
)

type KelasRepositoryInterface interface {
	GetJmlKelas(ctx context.Context) (*model.JmlKelas, error)
	GetKelas(ctx context.Context) ([]model.Kelas, error)
	GetKelasById(ctx context.Context, kelas_id uint32) (*model.Kelas, error)
	InsertKelas(ctx context.Context, data *dto_v1.KelasRequest) (*model.Kelas, error)
	UpdateKelas(ctx context.Context, kelas_id uint32, data *dto_v1.KelasRequest) (*model.Kelas, error)
	DeleteKelas(ctx context.Context, kelas_id uint32) (*model.Kelas, error)
}

type kelasRepository struct {
	db *sql.DB
}

func InitKelasRepository(db *sql.DB) KelasRepositoryInterface {
	return &kelasRepository{db: db}
}

func (r *kelasRepository) GetJmlKelas(ctx context.Context) (*model.JmlKelas, error) {
	data := &model.JmlKelas{}

	query := fmt.Sprintf(`
		SELECT COUNT(*) FROM %s
	`, model.TableKelas)

	err := r.db.QueryRowContext(ctx, query).Scan(&data.Jml)
	if err != nil {
		return nil, err
	}

	return data, nil
}

func (r *kelasRepository) GetKelas(ctx context.Context) ([]model.Kelas, error) {
	var data []model.Kelas

	query := fmt.Sprintf(`
		SELECT a.id, a.nama_kelas, a.status FROM %s as a
		ORDER BY a.nama_kelas ASC
	`, model.TableKelas)

	rows, err := r.db.QueryContext(ctx, query)
	if err != nil {
		return nil, err
	}

	defer rows.Close()

	for rows.Next() {
		var kelas model.Kelas
		if err := rows.Scan(&kelas.ID, &kelas.NamaKelas, &kelas.Status); err != nil {
			return nil, err
		}
		data = append(data, kelas)
	}

	// Check for any error encountered during row iteration
	if err := rows.Err(); err != nil {
		return nil, fmt.Errorf("data tidak ditemukan")
	}

	return data, nil
}

func (r *kelasRepository) GetKelasById(ctx context.Context, kelas_id uint32) (*model.Kelas, error) {
	kelas := &model.Kelas{}
	query := fmt.Sprintf(`
		SELECT a.id, a.nama_kelas, a.status FROM %s as a WHERE id = $1 LIMIT 1
	`, model.TableKelas)
	err := r.db.QueryRowContext(ctx, query, kelas_id).Scan(&kelas.ID, &kelas.NamaKelas, &kelas.Status)
	if err != nil {
		return nil, fmt.Errorf("ID kelas tidak ditemukan")
	}

	return kelas, nil
}

func (r *kelasRepository) InsertKelas(ctx context.Context, data *dto_v1.KelasRequest) (*model.Kelas, error) {
	tx, err := r.db.BeginTx(ctx, nil)
	if err != nil {
		return nil, err
	}
	defer tx.Rollback()

	query := fmt.Sprintf(`
		INSERT INTO %s (nama_kelas, status, created_at, updated_at)
		VALUES ($1, $2, NOW(), NOW())
		RETURNING id, nama_kelas, status, created_at, updated_at
	`, model.TableKelas)
	row := tx.QueryRowContext(ctx, query, data.NamaKelas, data.Status)

	var kelas model.Kelas
	if err := row.Scan(&kelas.ID, &kelas.NamaKelas, &kelas.Status, &kelas.CreatedAt, &kelas.UpdatedAt); err != nil {
		return nil, err
	}

	if err := tx.Commit(); err != nil {
		return nil, err
	}

	return &kelas, nil
}

func (r *kelasRepository) UpdateKelas(ctx context.Context, kelas_id uint32, data *dto_v1.KelasRequest) (*model.Kelas, error) {
	tx, err := r.db.BeginTx(ctx, nil)
	if err != nil {
		return nil, err
	}
	defer tx.Rollback()

	query := fmt.Sprintf(`
		UPDATE %s
		SET nama_kelas = $1, status = $2, updated_at = NOW()
		WHERE id = $3
		RETURNING id, nama_kelas, status, created_at, updated_at
	`, model.TableKelas)

	row := tx.QueryRowContext(ctx, query, data.NamaKelas, data.Status, kelas_id)

	var kelas model.Kelas
	if err := row.Scan(&kelas.ID, &kelas.NamaKelas, &kelas.Status, &kelas.CreatedAt, &kelas.UpdatedAt); err != nil {
		return nil, err
	}

	if err := tx.Commit(); err != nil {
		return nil, err
	}

	return &kelas, nil
}

func (r *kelasRepository) DeleteKelas(ctx context.Context, kelas_id uint32) (*model.Kelas, error) {
	tx, err := r.db.BeginTx(ctx, nil)
	if err != nil {
		return nil, err
	}
	defer tx.Rollback()

	// Ambil data sebelum dihapus untuk dikembalikan
	kelas, err := r.GetKelasById(ctx, kelas_id)
	if err != nil {
		return nil, fmt.Errorf("ID kelas tidak ditemukan")
	}

	query := fmt.Sprintf(`
		DELETE FROM %s WHERE id = $1
	`, model.TableKelas)

	if _, err := tx.ExecContext(ctx, query, kelas_id); err != nil {
		return nil, err
	}

	if err := tx.Commit(); err != nil {
		return nil, err
	}

	return kelas, nil
}
