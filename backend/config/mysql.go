package config

import (
	"database/sql"
	"fmt"
	"time" // Import time package for MySQL connection parameters

	_ "github.com/go-sql-driver/mysql" // MySQL driver
)

func NewMySQLConnection(host, port, user, password, dbName string) (*sql.DB, error) {
	// DSN (Data Source Name) for MySQL
	dsn := fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?charset=utf8mb4&parseTime=True&loc=Local",
		user, password, host, port, dbName)

	db, err := sql.Open("mysql", dsn)
	if err != nil {
		return nil, fmt.Errorf("failed to open MySQL connection: %w", err)
	}

	// Set connection pool properties (optional but recommended)
	db.SetMaxOpenConns(25)                 // Max number of open connections
	db.SetMaxIdleConns(25)                 // Max number of connections in the idle connection pool
	db.SetConnMaxLifetime(5 * time.Minute) // Max amount of time a connection may be reused

	// Ping the database to verify the connection
	if err = db.Ping(); err != nil {
		return nil, fmt.Errorf("failed to connect to MySQL database: %w", err)
	}

	fmt.Println("Successfully connected to MySQL database!")
	return db, nil
}
