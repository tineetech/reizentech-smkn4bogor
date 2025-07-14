package helpers

import (
	"context"
	"fmt"
	"reflect"
	"strconv"
	"strings"

	"github.com/go-playground/validator/v10"
	"github.com/muhammadridwansurya/api-go/repository"
)

// CustomValidator struct untuk validasi kustom
type CustomValidator struct {
	Validator *validator.Validate
	valRepo   repository.ValidationRepositoryInterface
}

// InitCustomValidation menginisialisasi validasi kustom
func InitCustomValidation(valRepo repository.ValidationRepositoryInterface) *CustomValidator {
	v := validator.New()
	customValidator := &CustomValidator{
		Validator: v,
		valRepo:   valRepo,
	}
	v.RegisterValidationCtx("unique", customValidator.UniqueValidation)
	v.RegisterValidationCtx("unique_twoparam", customValidator.UniqueWithTwoParamValidation)
	v.RegisterValidationCtx("in", customValidator.InValidation)
	return customValidator
}

// Implementasi metode Validate untuk memenuhi interface echo.Validator
func (cv *CustomValidator) Validate(i interface{}) error {
	return cv.Validator.Struct(i)
}

// UniqueValidation untuk memeriksa apakah nilai sudah ada di database
func (v *CustomValidator) UniqueValidation(ctx context.Context, fl validator.FieldLevel) bool {
	param := fl.Param()          // Contoh: "username.users"
	value := fl.Field().String() // Value dari field (misal: "john_doe")

	// Pisahkan parameter menjadi field dan table
	parts := strings.Split(param, ".")
	table := parts[0]
	field := parts[1]
	if len(parts) > 3 {
		return false // Format parameter salah
	}
	var paramField string = ""
	var paramFieldValue string = ""
	if len(parts) == 3 {
		paramField = parts[2]
		paramFieldValue = fl.Parent().FieldByName(paramField).String()
	}

	// Panggil repository untuk cek apakah data unique
	isUnique, err := v.valRepo.IsUnique(ctx, table, field, value, paramField, paramFieldValue)
	if err != nil {
		fmt.Println("Error checking uniqueness:", err)
		return false
	}

	return isUnique
}

// UniqueValidation untuk memeriksa apakah nilai sudah ada di database
func (v *CustomValidator) UniqueWithTwoParamValidation(ctx context.Context, fl validator.FieldLevel) bool {
	param := fl.Param()           // Contoh: "users.username"
	value1 := fl.Field().String() // Value dari field (misal: "john_doe")

	// Pisahkan parameter menjadi field dan table
	parts := strings.Split(param, ".")
	table := parts[0]
	field1 := parts[1]
	field2 := parts[2]
	value2 := fl.Parent().FieldByName(field2).String()
	if len(parts) > 4 {
		return false // Format parameter salah
	}
	var key string = ""
	var keyValue string = ""
	if len(parts) == 4 {
		key = parts[3]
		keyValue = fl.Parent().FieldByName(key).String()
	}

	// Panggil repository untuk cek apakah data unique
	isUnique, err := v.valRepo.IsUniqueTwoParam(ctx, table, field1, value1, field2, value2, key, keyValue)
	if err != nil {
		fmt.Println("Error checking uniqueness:", err)
		return false
	}

	return isUnique
}

func (v *CustomValidator) InValidation(ctx context.Context, fl validator.FieldLevel) bool {
	// Ambil nilai field yang sedang divalidasi
	value := fl.Field().String()

	// Ambil parameter dari tag, misal: "admin.user.superadmin"
	param := fl.Param()
	allowed := strings.Split(param, ".")

	// Cek apakah value ada di daftar allowed
	for _, item := range allowed {
		if value == item {
			return true
		}
	}

	return false
}

// FIELDNYA PAKE JSON
func MapJSONFields(structType reflect.Type) map[string]string {
	jsonMap := make(map[string]string)

	for i := 0; i < structType.NumField(); i++ {
		field := structType.Field(i)
		jsonName := field.Name

		if tag, ok := field.Tag.Lookup("json"); ok {
			jsonName = strings.Split(tag, ",")[0]
		}

		jsonMap[field.Name] = jsonName
	}

	return jsonMap
}

// HandleValidationErrors untuk menangani error validasi
func HandleValidationErrors(err error, fieldMap map[string]string) map[string]string {
	errors := make(map[string]string)

	for _, err := range err.(validator.ValidationErrors) {
		jsonField := fieldMap[err.Field()] // Ambil dari mapping

		switch err.Tag() {
		case "required":
			errors[jsonField] = fmt.Sprintf("%s wajib diisi", jsonField)
		case "min":
			errors[jsonField] = fmt.Sprintf("%s minimal %s karakter", jsonField, err.Param())
		case "max":
			errors[jsonField] = fmt.Sprintf("%s maksimal %s karakter", jsonField, err.Param())
		case "unique":
			errors[jsonField] = fmt.Sprintf("%s sudah digunakan", jsonField)
		case "in":
			errors[jsonField] = fmt.Sprintf("%s format tidak valid", jsonField)
		case "numeric":
			errors[jsonField] = fmt.Sprintf("%s hanya angka saja", jsonField)
		case "email":
			errors[jsonField] = "format email tidak valid"
		default:
			errors[jsonField] = fmt.Sprintf("%s tidak valid", jsonField)
		}
	}

	return errors
}
