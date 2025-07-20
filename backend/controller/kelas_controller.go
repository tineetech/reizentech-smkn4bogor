package controller

import (
	"net/http"
	"strconv"

	"github.com/labstack/echo/v4"
	dto "github.com/muhammadridwansurya/api-go/dto"
	"github.com/muhammadridwansurya/api-go/helpers"
	"github.com/muhammadridwansurya/api-go/middlewares"
	service "github.com/muhammadridwansurya/api-go/service"
)

type KelasController struct {
	kelasService service.KelasServiceInterface
}

func InitKelasController(e *echo.Echo, service service.KelasServiceInterface, middleware middlewares.Middlewares) {
	controller := &KelasController{kelasService: service}

	route := e.Group("/api/kelas") // sekalian menerapkan middleware untuk grup ini
	route.GET("", controller.GetKelas)
	route.GET("/:kelas_id", controller.GetKelasById)
	route.GET("/jumlah", controller.GetJmlKelas)
	route.POST("", controller.InsertKelas)
	route.PUT("/:kelas_id", controller.UpdateKelas)
	route.DELETE("/:kelas_id", controller.DeleteKelas)
}

func (c *KelasController) GetKelas(ctx echo.Context) error {
	data, err := c.kelasService.GetDaftarKelas(ctx.Request().Context())
	if err != nil {
		return ctx.JSON(http.StatusNotFound, helpers.BasicResponse(
			false,
			err.Error(),
		))
	}

	// Jika berhasil login, kembalikan user dalam respons
	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(
		true,
		"data ditemukan",
		data,
	))
}

func (c *KelasController) GetKelasById(ctx echo.Context) error {
	idParam := ctx.Param("kelas_id")
	id, err := strconv.Atoi(idParam)
	if err != nil || id <= 0 {
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(
			false,
			"ID tidak valid",
		))
	}

	data, err := c.kelasService.GetKelasById(ctx.Request().Context(), uint32(id))
	if err != nil {
		return ctx.JSON(http.StatusNotFound, helpers.BasicResponse(false, err.Error()))
	}

	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(true, "data ditemukan", data))
}

func (c *KelasController) GetJmlKelas(ctx echo.Context) error {
	data, err := c.kelasService.GetJumlahKelas(ctx.Request().Context())
	if err != nil {
		return ctx.JSON(http.StatusNotFound, helpers.BasicResponse(
			false,
			err.Error(),
		))
	}

	// Jika berhasil login, kembalikan user dalam respons
	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(
		true,
		"data ditemukan",
		data,
	))
}

func (c *KelasController) InsertKelas(ctx echo.Context) error {
	// Bind dan validasi request
	request := new(dto.KelasRequest)
	if err := helpers.BindAndValidate(ctx, request); err != nil {
		if validationErr, ok := err.(*helpers.ValidationError); ok {
			// Jika error adalah ValidationError, kembalikan respons JSON
			return ctx.JSON(http.StatusBadRequest, helpers.ErrorResponseRequest(
				false,
				validationErr.Message,
				validationErr.Errors,
			))
		}
		// Error lainnya
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(
			false,
			err.Error(),
		))
	}

	data, err := c.kelasService.CreateKelas(ctx.Request().Context(), request)
	if err != nil {
		return ctx.JSON(http.StatusInternalServerError, helpers.BasicResponse(
			false,
			err.Error(),
		))
	}

	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(
		true,
		"berhasil menambah data",
		data,
	))
}

func (c *KelasController) UpdateKelas(ctx echo.Context) error {
	idParam := ctx.Param("kelas_id")
	id, err := strconv.Atoi(idParam)
	if err != nil || id <= 0 {
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(false, "ID tidak valid"))
	}

	request := new(dto.KelasRequest)
	if err := helpers.BindAndValidate(ctx, request); err != nil {
		if validationErr, ok := err.(*helpers.ValidationError); ok {
			return ctx.JSON(http.StatusBadRequest, helpers.ErrorResponseRequest(false, validationErr.Message, validationErr.Errors))
		}
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(false, err.Error()))
	}

	data, err := c.kelasService.UpdateKelas(ctx.Request().Context(), uint32(id), request)
	if err != nil {
		return ctx.JSON(http.StatusInternalServerError, helpers.BasicResponse(false, err.Error()))
	}

	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(true, "berhasil mengubah data", data))
}

func (c *KelasController) DeleteKelas(ctx echo.Context) error {
	idParam := ctx.Param("kelas_id")
	id, err := strconv.Atoi(idParam)
	if err != nil || id <= 0 {
		return ctx.JSON(http.StatusBadRequest, helpers.BasicResponse(false, "ID tidak valid"))
	}

	data, err := c.kelasService.DeleteKelas(ctx.Request().Context(), uint32(id))
	if err != nil {
		return ctx.JSON(http.StatusInternalServerError, helpers.BasicResponse(false, err.Error()))
	}

	return ctx.JSON(http.StatusOK, helpers.SuccessResponseWithData(true, "berhasil menghapus data", data))
}
