package repository

import (
	"context"
	"database/sql"
	"fmt"
)

type ValidationRepositoryInterface interface {
	IsUnique(ctx context.Context, table, column, value string, id uint32) (bool, error)
}

type validationRepository struct {
	db *sql.DB
}

func InitValidationRepository(db *sql.DB) ValidationRepositoryInterface {
	return &validationRepository{db: db}
}

func (r *validationRepository) IsUnique(ctx context.Context, table, column, value string, id uint32) (bool, error) {
	var query string
	var err error
	var result int8

	if id != 0 {
		// Query untuk update: cek unique tapi exclude id tertentu
		query = fmt.Sprintf("SELECT 1 FROM %s WHERE %s = $1 AND id != $2 LIMIT 1", table, column) // untuk kondisi update
		err = r.db.QueryRowContext(ctx, query, value, id).Scan(&result)
	} else {
		// Query untuk insert: cek unique biasa
		query = fmt.Sprintf("SELECT 1 FROM %s WHERE %s = $1 LIMIT 1", table, column) //  Hanya memeriksa apakah ada satu baris yang cocok dengan kondisi
		err = r.db.QueryRowContext(ctx, query, value).Scan(&result)
	}

	if err == sql.ErrNoRows {
		// Tidak ada baris yang cocok, berarti data unik
		return true, nil
	} else if err != nil {
		// Kesalahan query lainnya
		return false, err
	}
	// Jika ada baris yang cocok, data tidak unik
	return false, nil
}
