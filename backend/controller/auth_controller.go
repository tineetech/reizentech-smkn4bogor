package controller

import (
	"net/http"

	"github.com/labstack/echo/v4"
	dto "github.com/muhammadridwansurya/api-go/dto"
	"github.com/muhammadridwansurya/api-go/helpers"
	"github.com/muhammadridwansurya/api-go/middlewares"
	service "github.com/muhammadridwansurya/api-go/service"
)

type AuthController struct {
	authService service.AuthServiceInterface
}

func InitAuthController(e *echo.Echo, svc service.AuthServiceInterface, mw middlewares.Middlewares) {
	ctrl := &AuthController{authService: svc}

	public := e.Group("/api/auth")
	public.POST("/register", ctrl.Register)
	public.POST("/login", ctrl.Login)

	private := e.Group("/api/auth")
	private.POST("/logout", ctrl.Logout, mw.JWT)
	private.GET("/me", ctrl.Profile, mw.JWT)
}

func (c *AuthController) Register(ctx echo.Context) error {
	req := new(dto.RegisterRequest)
	if err := helpers.BindAndValidate(ctx, req); err != nil {
		if vErr, ok := err.(*helpers.ValidationError); ok {
			return ctx.JSON(http.StatusBadRequest, helpers.ErrorResponseRequest(false, vErr.Message, vErr.Errors))
		}
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(false, err.Error()))
	}

	user, err := c.authService.Register(ctx.Request().Context(), req)
	if err != nil {
		return ctx.JSON(http.StatusInternalServerError, helpers.BasicResponse(false, err.Error()))
	}

	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(true, "registrasi berhasil", user))
}

func (c *AuthController) Login(ctx echo.Context) error {
	req := new(dto.LoginRequest)
	if err := helpers.BindAndValidate(ctx, req); err != nil {
		if vErr, ok := err.(*helpers.ValidationError); ok {
			return ctx.JSON(http.StatusBadRequest, helpers.ErrorResponseRequest(false, vErr.Message, vErr.Errors))
		}
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(false, err.Error()))
	}

	user, token, err := c.authService.Login(ctx.Request().Context(), req)
	if err != nil {
		return ctx.JSON(http.StatusUnauthorized, helpers.BasicResponse(false, err.Error()))
	}

	resp := map[string]interface{}{
		"user":  user,
		"token": token,
	}

	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(true, "login berhasil", resp))
}

func (c *AuthController) Logout(ctx echo.Context) error {
	claims := helpers.GetUserClaims(ctx)
	if claims == nil {
		return ctx.JSON(http.StatusUnauthorized, helpers.BasicResponse(false, "token tidak valid"))
	}

	if err := c.authService.Logout(ctx.Request().Context(), claims.UserID); err != nil {
		return ctx.JSON(http.StatusInternalServerError, helpers.BasicResponse(false, err.Error()))
	}

	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(true, "logout berhasil", nil))
}

func (c *AuthController) Profile(ctx echo.Context) error {
	claims := helpers.GetUserClaims(ctx)
	if claims == nil {
		return ctx.JSON(http.StatusUnauthorized, helpers.BasicResponse(false, "token tidak valid"))
	}

	user, err := c.authService.GetProfile(ctx.Request().Context(), claims.UserID)
	if err != nil {
		return ctx.JSON(http.StatusNotFound, helpers.BasicResponse(false, err.Error()))
	}

	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(true, "profil ditemukan", user))
}
