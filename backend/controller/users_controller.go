package controller

import (
	"net/http"

	"github.com/labstack/echo/v4"
	"github.com/muhammadridwansurya/api-go/dto"
	"github.com/muhammadridwansurya/api-go/helpers"
	mw "github.com/muhammadridwansurya/api-go/middlewares"
	service "github.com/muhammadridwansurya/api-go/service"
)

type UsersController struct {
	usersService service.UsersServiceInterface
}

// -------------------------------------------------------------------
// Inisialisasi routes (gunakan middleware grup bila perlu)
// -------------------------------------------------------------------
func InitUsersController(e *echo.Echo, s service.UsersServiceInterface, m mw.Middlewares) {
	h := &UsersController{usersService: s}

	r := e.Group("/api/users", m.KeyApi)
	r.GET("", h.GetUsers)
	r.GET("/:user_id", h.GetUserByID)
	r.POST("", h.CreateUser)
	r.PUT("/:user_id", h.UpdateUser)
	r.DELETE("/:user_id", h.DeleteUser)
}

// -------------------------------------------------------------------
// Handlers
// -------------------------------------------------------------------
func (c *UsersController) GetUsers(ctx echo.Context) error {
	data, err := c.usersService.GetDaftarUsers(ctx.Request().Context())
	if err != nil {
		return ctx.JSON(http.StatusNotFound, helpers.BasicResponse(false, err.Error()))
	}
	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(true, "data ditemukan", data))
}

func (c *UsersController) GetUserByID(ctx echo.Context) error {
	id := ctx.Param("user_id")
	if id == "" {
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(false, "ID tidak valid"))
	}
	data, err := c.usersService.GetUserByID(ctx.Request().Context(), id)
	if err != nil {
		return ctx.JSON(http.StatusNotFound, helpers.BasicResponse(false, err.Error()))
	}
	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(true, "data ditemukan", data))
}

func (c *UsersController) CreateUser(ctx echo.Context) error {
	req := new(dto.UserRequest)
	if err := helpers.BindAndValidate(ctx, req); err != nil {
		if vErr, ok := err.(*helpers.ValidationError); ok {
			return ctx.JSON(http.StatusBadRequest, helpers.ErrorResponseRequest(false, vErr.Message, vErr.Errors))
		}
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(false, err.Error()))
	}

	data, err := c.usersService.CreateUser(ctx.Request().Context(), req)
	if err != nil {
		return ctx.JSON(http.StatusInternalServerError, helpers.BasicResponse(false, err.Error()))
	}
	return ctx.JSON(http.StatusCreated, helpers.SuccessResponseWithData(true, "berhasil menambah data", data))
}

func (c *UsersController) UpdateUser(ctx echo.Context) error {
	id := ctx.Param("user_id")
	if id == "" {
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(false, "ID tidak valid"))
	}
	req := new(dto.UserRequest)
	if err := helpers.BindAndValidate(ctx, req); err != nil {
		if vErr, ok := err.(*helpers.ValidationError); ok {
			return ctx.JSON(http.StatusBadRequest, helpers.ErrorResponseRequest(false, vErr.Message, vErr.Errors))
		}
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(false, err.Error()))
	}
	data, err := c.usersService.UpdateUser(ctx.Request().Context(), id, req)
	if err != nil {
		return ctx.JSON(http.StatusInternalServerError, helpers.BasicResponse(false, err.Error()))
	}
	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(true, "berhasil mengubah data", data))
}

func (c *UsersController) DeleteUser(ctx echo.Context) error {
	id := ctx.Param("user_id")
	if id == "" {
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(false, "ID tidak valid"))
	}
	data, err := c.usersService.DeleteUser(ctx.Request().Context(), id)
	if err != nil {
		return ctx.JSON(http.StatusInternalServerError, helpers.BasicResponse(false, err.Error()))
	}
	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(true, "berhasil menghapus data", data))
}
