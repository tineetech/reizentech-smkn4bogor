package routes

import (
	"database/sql"

	"github.com/labstack/echo/v4"
	controller "github.com/muhammadridwansurya/api-go/controller"
	"github.com/muhammadridwansurya/api-go/middlewares"
	repository "github.com/muhammadridwansurya/api-go/repository"
	service "github.com/muhammadridwansurya/api-go/service"
)

type Repositories struct {
	AuthRepository repository.AuthRepositoryInterface
	UserRepository repository.UserRepositoryInterface
}

type Services struct {
	AuthService  service.AuthServiceInterface
	UsersService service.UsersServiceInterface
}

func InitRepositories(db *sql.DB) *Repositories {
	return &Repositories{
		AuthRepository: repository.InitAuthRepository(db),
		UserRepository: repository.InitUserRepository(db),
	}
}

func InitServices(repo *Repositories) *Services {
	return &Services{
		AuthService:  service.InitAuthService(repo.AuthRepository),
		UsersService: service.InitUsersService(repo.UserRepository),
	}
}

func InitRoutes(e *echo.Echo, svcs *Services, mw middlewares.Middlewares) {
	controller.InitAuthController(e, svcs.AuthService, mw)

	controller.InitUsersController(e, svcs.UsersService, mw)
}
