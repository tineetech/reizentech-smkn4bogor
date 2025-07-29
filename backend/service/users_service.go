package service

import (
	"context"

	"github.com/muhammadridwansurya/api-go/dto"
	"github.com/muhammadridwansurya/api-go/model"
	repo "github.com/muhammadridwansurya/api-go/repository"
	"golang.org/x/crypto/bcrypt"
)

type UsersServiceInterface interface {
	GetDaftarUsers(ctx context.Context) ([]model.User, error)
	GetUserByID(ctx context.Context, id string) (*model.User, error)
	UpdateUser(ctx context.Context, id string, req *dto.UserRequest) (*model.User, error)
	DeleteUser(ctx context.Context, id string) (*model.User, error)
}

type usersService struct{ repo repo.UserRepositoryInterface }

func InitUsersService(r repo.UserRepositoryInterface) UsersServiceInterface {
	return &usersService{repo: r}
}

func (s *usersService) GetDaftarUsers(ctx context.Context) ([]model.User, error) {
	return s.repo.GetDaftarUsers(ctx)
}

func (s *usersService) GetUserByID(ctx context.Context, id string) (*model.User, error) {
	return s.repo.GetUserByID(ctx, id)
}

func (s *usersService) UpdateUser(ctx context.Context, id string, req *dto.UserRequest) (*model.User, error) {
	if req.Password != "" {
		hash, err := bcrypt.GenerateFromPassword([]byte(req.Password), bcrypt.DefaultCost)
		if err != nil {
			return nil, err
		}
		req.PasswordHash = string(hash)
	}
	return s.repo.UpdateUser(ctx, id, req)
}

func (s *usersService) DeleteUser(ctx context.Context, id string) (*model.User, error) {
	return s.repo.DeleteUser(ctx, id)
}
