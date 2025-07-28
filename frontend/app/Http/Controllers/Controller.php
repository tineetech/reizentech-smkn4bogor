<?php

namespace App\Http\Controllers;
use App\Services\AuthMenuService;

abstract class Controller
{
    protected $sessionName;
    protected $authMenuService;

    public function __construct(AuthMenuService $authMenuService)
    {
        $this->sessionName = config('app.session_name');
        $this->authMenuService = $authMenuService;
    }
}
