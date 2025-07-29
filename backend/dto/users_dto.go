package dto

import (
	"time"

	"github.com/muhammadridwansurya/api-go/model"
)

type UserRequest struct {
	Username    string           `json:"username" validate:"required"`
	Email       string           `json:"email"`
	PhoneNumber string           `json:"phone_number"`
	Password    string           `json:"password,omitempty"`
	Status      model.UserStatus `json:"status" validate:"required,oneof=ACTIVE INACTIVE BANNED"`

	PasswordHash string `json:"-"`
}

type UserBiodata struct {
	ID          string    `json:"id"`
	UserID      string    `json:"user_id"`
	UserCode    string    `json:"user_code"`
	Name        string    `json:"name"`
	Sex         string    `json:"sex"` // 'L' atau 'P'
	DateOfBirth time.Time `json:"date_of_birth"`
	Status      bool      `json:"status"`
	CreatedAt   time.Time `json:"created_at"`
	UpdatedAt   time.Time `json:"updated_at"`
}
