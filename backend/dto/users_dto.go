package dto

import "github.com/muhammadridwansurya/api-go/model"

type UserRequest struct {
	Username    string           `json:"username" validate:"required"`
	Email       string           `json:"email"`
	PhoneNumber string           `json:"phone_number"`
	Password    string           `json:"password,omitempty"`
	Status      model.UserStatus `json:"status" validate:"required,oneof=ACTIVE INACTIVE BANNED"`

	PasswordHash string `json:"-"`
}
