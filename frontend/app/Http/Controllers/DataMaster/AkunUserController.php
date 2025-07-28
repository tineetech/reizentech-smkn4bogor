<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\AuthMenuService;
use App\Services\AkunUserService;

class AkunUserController extends Controller
{
    protected $mainMenuCode = 'DM';
    protected $subMenuCode = 'USR';
    protected $levelMenuCode = 'AU';
    protected $userLevelId = false;
    protected $breadcrumb = [];
    protected $roleMenuAccess = "";
    protected $menuDinamis = [];
    protected $urlMenu = [];
    protected $request;

    // service
    protected $akunUserService;

    public function __construct(AuthMenuService $autMenuService, AkunUserService $akunUserService, Request $request)
    {
        parent::__construct($autMenuService);
        $this->akunUserService = $akunUserService;
        $this->request = $request;

        $this->userLevelId = session()->get($this->sessionName.".user_level_id");
        $this->authMenuService->checkAuthMenu($this->userLevelId, $this->mainMenuCode, $this->subMenuCode, $this->levelMenuCode);

        $this->roleMenuAccess = session()->get($this->sessionName.".role_menu_access");
        $this->menuDinamis = $this->authMenuService->getMenuDinamisLogged($this->roleMenuAccess, $this->mainMenuCode, $this->subMenuCode, $this->levelMenuCode);
        $this->breadcrumb = [
            [
				'name' => 'Data Master',
				'url' => '#',
				'active' => false
			],
            [
				'name' => 'Users',
				'url' => route('data_master.users.akun_user.view'),
				'active' => false
			],
            [
				'name' => 'Akun User',
				'url' => route('data_master.users.akun_user.view'),
				'active' => true
			],
        ];
    }

    public function viewAkunUser()
    {
        if ($redirect = $this->authMenuService->checkView()) return $redirect;

        $data = [
			'title' =>  'Akun User',
			'menuDinamis' => $this->menuDinamis,
			'breadcrumb' => $this->breadcrumb,
            'urlMenu' => $this->urlMenu = [
                'link' => route('data_master.users.akun_user.view'),
                'link_role' => route('data_master.users.akun_user.view'),
                'link_json' => route('data_master.users.akun_user.json_data'),
            ]
		];
        return view('pages.data_master.users.akun_user', $data);
    }

    public function showAkunUserById($idAkunUser)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->akunUserService->getDataAkunUserJsonByIdUser($idAkunUser);
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function jsonDataAkunUser()
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->akunUserService->getDataAkunUserJson();
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function createDataAkunUser()
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonCreate()) return $redirect;
            $data = $this->request->only(['username', 'email', 'phone_number', 'password', 'status']);
            return $this->akunUserService->createDataAkunUser($data);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function updateDataAkunUser($idAkunUser)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonUpdate()) return $redirect;
            $data = $this->request->only(['username', 'email', 'phone_number', 'password', 'status']);
            return $this->akunUserService->updateDataAkunUser($data, $idAkunUser);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function deleteDataAkunUser($idAkunUser)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonDelete()) return $redirect;
            return $this->akunUserService->deleteDataAkunUser($idAkunUser);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    // USER ROLE

    public function showAkunUserRoleById($idAkunUser)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->akunUserService->getDataUserRoleJsonByIdUserRole($idAkunUser);
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function jsonDataAkunUserRole($idUserRole)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->akunUserService->getDataUserRoleJson($idUserRole);
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function createDataAkunUserRole($idApp, $idUser)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonCreate()) return $redirect;
            $data = $this->request->only(['user_id', 'user_level_id', 'status']);
            return $this->akunUserService->createDataUserRole($data, $idApp, $idUser);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function updateDataAkunUserRole($idApp, $idUserRole)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonUpdate()) return $redirect;
            $data = $this->request->only(['user_id', 'user_level_id', 'status']);
            return $this->akunUserService->updateDataUserRole($data, $idApp, $idUserRole);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function deleteDataAkunUserRole($idUserRole)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonDelete()) return $redirect;
            return $this->akunUserService->deleteDataUserRole($idUserRole);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }
}
