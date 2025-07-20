package routes

import (
	"database/sql"
	"net/http"

	"github.com/labstack/echo/v4"
	controller "github.com/muhammadridwansurya/api-go/controller"
	"github.com/muhammadridwansurya/api-go/middlewares"
	repository "github.com/muhammadridwansurya/api-go/repository"
	service "github.com/muhammadridwansurya/api-go/service"
)

type Repositories struct {
	KelasRepository repository.KelasRepositoryInterface
}

type Services struct {
	KelasService service.KelasServiceInterface
}

func InitRepositories(db *sql.DB) *Repositories {
	return &Repositories{
		KelasRepository: repository.InitKelasRepository(db),
	}
}

func InitServices(repo *Repositories) *Services {
	return &Services{
		KelasService: service.InitKelasService(repo.KelasRepository),
	}
}

func InitRoutes(e *echo.Echo, services Services, middleware middlewares.Middlewares) {

	controller.InitKelasController(e, services.KelasService, middleware)
	e.GET("/", func(c echo.Context) error {
		return c.JSON(http.StatusOK, echo.Map{
			"message": "Selamat datang di API Go saya! haha",
			"status":  "success",
			"version": "1.0.0",
		})
	})

}
