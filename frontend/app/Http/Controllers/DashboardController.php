<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\AuthMenuService;

class DashboardController extends Controller
{
    protected $mainMenuCode = 'DSB';
    protected $subMenuCode = null;
    protected $levelMenuCode = null;
    protected $userLevelId = false;
    protected $breadcrumb = [];
    protected $roleMenuAccess = "";
    protected $menuDinamis = [];
    protected $urlMenu = [];

    public function __construct(AuthMenuService $autMenuService)
    {
        parent::__construct($autMenuService);
        $this->userLevelId = session()->get($this->sessionName.".user_level_id");
        $this->authMenuService->checkAuthMenu($this->userLevelId, $this->mainMenuCode, $this->subMenuCode, $this->levelMenuCode);

        $this->roleMenuAccess = session()->get($this->sessionName.".role_menu_access");
        $this->menuDinamis = $this->authMenuService->getMenuDinamisLogged($this->roleMenuAccess, $this->mainMenuCode, $this->subMenuCode, $this->levelMenuCode);
        $this->breadcrumb = [
            [
				'name' => 'Dashboard',
				'url' => route('dashboard'),
				'active' => true
			],
        ];
    }

    public function viewDashboard()
    {
        if ($redirect = $this->authMenuService->checkView()) return $redirect;

        $data = [
			'title' =>  'Dashboard',
			'menuDinamis' => $this->menuDinamis,
			'breadcrumb' => $this->breadcrumb,
		];
        return view('pages.dashboard', $data);
    }
}
