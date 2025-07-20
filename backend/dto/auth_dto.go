package dto

type RegisterRequest struct {
	Username    string  `json:"username"    form:"username"      validate:"required,alphanum"`
	Email       *string `json:"email,omitempty"        form:"email"        validate:"omitempty,email"`
	PhoneNumber *string `json:"phone_number,omitempty" form:"phone_number"`
	Password    string  `json:"password"    form:"password"      validate:"required,min=8"`
}

type LoginRequest struct {
	Email    string `json:"email" form:"email" validate:"required"`
	Password string `json:"password" form:"password" validate:"required"`
}
