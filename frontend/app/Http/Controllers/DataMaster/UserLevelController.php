<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\AuthMenuService;
use App\Services\UserLevelService;

class UserLevelController extends Controller
{
    protected $mainMenuCode = 'DM';
    protected $subMenuCode = 'UL';
    protected $levelMenuCode = null;
    protected $userLevelId = false;
    protected $breadcrumb = [];
    protected $roleMenuAccess = "";
    protected $menuDinamis = [];
    protected $urlMenu = [];
    protected $request;

    // service
    protected $userLevelService;

    public function __construct(AuthMenuService $autMenuService, UserLevelService $userLevelService, Request $request)
    {
        parent::__construct($autMenuService);
        $this->userLevelService = $userLevelService;
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
				'name' => 'User Level',
				'url' => route('data_master.user_level.view'),
				'active' => true
			],
        ];
    }

    public function viewUserLevel()
    {
        if ($redirect = $this->authMenuService->checkView()) return $redirect;

        $data = [
			'title' =>  'User Level',
			'menuDinamis' => $this->menuDinamis,
			'breadcrumb' => $this->breadcrumb,
            'urlMenu' => $this->urlMenu = [
                'link' => route('data_master.user_level.view'),
                'link_json' => route('data_master.user_level.json_data'),
                'link_options' => route('data_master.user_level.options'),
            ]
		];
        return view('pages.data_master.user_level', $data);
    }

    public function showUserLevelById($idUserLevel)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->userLevelService->getDataUserLevelJsonByIdUserLevel($idUserLevel);
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function getFormOptionsUserLevel()
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->userLevelService->getFormOptionsUserLevel();
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function jsonDataUserLevel()
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->userLevelService->getDataUserLevelJson();
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function createDataUserLevel()
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonCreate()) return $redirect;
            $data = $this->request->only(['biodata_ref_id', 'app_id', 'user_level_name', 'status']);
            return $this->userLevelService->createDataUserLevel($data);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function updateDataUserLevel($idUserLevel)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonUpdate()) return $redirect;
            $data = $this->request->only(['biodata_ref_id', 'app_id', 'user_level_name', 'status']);
            return $this->userLevelService->updateDataUserLevel($data, $idUserLevel);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function deleteDataUserLevel($idUserLevel)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonDelete()) return $redirect;
            return $this->userLevelService->deleteDataUserLevel($idUserLevel);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }
}
