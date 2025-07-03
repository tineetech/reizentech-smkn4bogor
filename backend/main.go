package main

import (
	"database/sql"
	"log"
	"os"

	"github.com/joho/godotenv"
	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
	"github.com/muhammadridwansurya/api-go/config"
	"github.com/muhammadridwansurya/api-go/helpers"
	"github.com/muhammadridwansurya/api-go/middlewares"
	"github.com/muhammadridwansurya/api-go/repository"
	"github.com/muhammadridwansurya/api-go/routes"
)

func initEnv() {
	if err := godotenv.Load(); err != nil {
		log.Fatal("Error loading .env file")
	}
}

func initLogFile(fileName string) *os.File {
	logFile, err := os.OpenFile(fileName, os.O_APPEND|os.O_CREATE|os.O_WRONLY, 0666)
	if err != nil {
		log.Fatal("Error opening log file:", err)
	}
	return logFile
}

func initDatabase() *sql.DB {
	db, err := config.NewPostgresConnection(
		os.Getenv("DB_HOST"),
		os.Getenv("DB_PORT"),
		os.Getenv("DB_USER"),
		os.Getenv("DB_PASSWORD"),
		os.Getenv("DB_NAME"),
	)
	if err != nil {
		log.Fatal(err)
	}
	return db
}

func initEcho(logFile *os.File, customValidator echo.Validator) *echo.Echo {
	e := echo.New()

	// Middleware
	e.Use(middleware.LoggerWithConfig(middleware.LoggerConfig{
		Skipper: middleware.DefaultSkipper,
		Format:  `${time_rfc3339} | ${id} | ${remote_ip} | ${host} | ${method} | ${uri} | ${user_agent} | ${status} | ${error} | ${latency} | ${latency_human} | ${bytes_in} | ${bytes_out}` + "\n",
		Output:  logFile,
	}))
	e.Use(middleware.Recover())
	e.Use(middleware.Secure())
	e.Use(middleware.CORS())

	// Custom validator
	e.Validator = customValidator

	e.Use(middleware.RateLimiterWithConfig(middlewares.InitRateLimiterConfig()))

	return e
}

func main() {
	// Initialize environment variables
	initEnv()

	// Initialize log file
	logFile := initLogFile("my_logs.log")
	defer logFile.Close()

	// Initialize database
	db := initDatabase()
	defer db.Close()

	// Initialize custom validator
	valRepo := repository.InitValidationRepository(db)
	customValidator := helpers.InitCustomValidation(valRepo)

	// Initialize Echo
	e := initEcho(logFile, customValidator)

	// Initialize middlewares, repositories, services, and routes
	middlewrs := middlewares.InitMiddlewares()
	repo := routes.InitRepositories(db)
	service := routes.InitServices(repo)
	routes.InitRoutes(e, *service, *middlewrs)

	// Start server
	e.Logger.Fatal(e.Start(":1323"))
}
