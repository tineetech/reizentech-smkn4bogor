package routes

import (
	"database/sql"

	"github.com/labstack/echo/v4"
	controller_v1 "github.com/muhammadridwansurya/api-go/controller/v1"
	"github.com/muhammadridwansurya/api-go/middlewares"
	repository_v1 "github.com/muhammadridwansurya/api-go/repository/v1"
	service_v1 "github.com/muhammadridwansurya/api-go/service/v1"
)

type Repositories struct {
	KelasRepository repository_v1.KelasRepositoryInterface
}

type Services struct {
	KelasService service_v1.KelasServiceInterface
}

func InitRepositories(db *sql.DB) *Repositories {
	return &Repositories{
		KelasRepository: repository_v1.InitKelasRepository(db),
	}
}

func InitServices(repo *Repositories) *Services {
	return &Services{
		KelasService: service_v1.InitKelasService(repo.KelasRepository),
	}
}

func InitRoutes(e *echo.Echo, services Services, middleware middlewares.Middlewares) {

	controller_v1.InitKelasController(e, services.KelasService, middleware)

}
