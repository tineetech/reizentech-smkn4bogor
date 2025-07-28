<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataMaster\AppController;
use App\Http\Controllers\DataMaster\BiodataRefController;
use App\Http\Controllers\DataMaster\UserLevelController;
use App\Http\Controllers\DataMaster\MainMenuController;
use App\Http\Controllers\DataMaster\RoleMenuController;
use App\Http\Controllers\DataMaster\AkunUserController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['k4.guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'viewLogin'])->name('auth.view');
    Route::post('/login', [AuthController::class, 'actionLogin'])->name('auth.login');
});

Route::middleware(['k4.auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'actionLogout'])->name('auth.logout');

    Route::get('/dashboard', [DashboardController::class, 'viewDashboard'])->name('dashboard');

    Route::prefix('data_master')->as('data_master.')->group(function () {
        Route::prefix('aplikasi')->as('app.')->group(function () {
            Route::get('/', [AppController::class, 'viewApp'])->name('view');
            Route::get('/json_data', [AppController::class, 'jsonDataApp'])->name('json_data');
            Route::get('/view/{idApp}', [AppController::class, 'showAppById'])->name('view.id');
            Route::post('/tambah', [AppController::class, 'createDataApp'])->name('create');
            Route::put('/update/{idApp}', [AppController::class, 'updateDataApp'])->name('update');
            Route::delete('/delete/{idApp}', [AppController::class, 'deleteDataApp'])->name('delete');
        });
        Route::prefix('biodata_ref')->as('biodata_ref.')->group(function () {
            Route::get('/', [BiodataRefController::class, 'viewBiodataRef'])->name('view');
            Route::get('/json_data', [BiodataRefController::class, 'jsonDataBiodataRef'])->name('json_data');
            Route::get('/view/{idBiodataRef}', [BiodataRefController::class, 'showBiodataRefById'])->name('view.id');
            Route::post('/tambah', [BiodataRefController::class, 'createDataBiodataRef'])->name('create');
            Route::put('/update/{idBiodataRef}', [BiodataRefController::class, 'updateDataBiodataRef'])->name('update');
            Route::delete('/delete/{idBiodataRef}', [BiodataRefController::class, 'deleteDataBiodataRef'])->name('delete');
        });
        Route::prefix('user_level')->as('user_level.')->group(function () {
            Route::get('/', [UserLevelController::class, 'viewUserLevel'])->name('view');
            Route::get('/json_data', [UserLevelController::class, 'jsonDataUserLevel'])->name('json_data');
            Route::get('/form/options', [UserLevelController::class, 'getFormOptionsUserLevel'])->name('options');
            Route::get('/view/{idUserLevel}', [UserLevelController::class, 'showUserLevelById'])->name('view.id');
            Route::post('/tambah', [UserLevelController::class, 'createDataUserLevel'])->name('create');
            Route::put('/update/{idUserLevel}', [UserLevelController::class, 'updateDataUserLevel'])->name('update');
            Route::delete('/delete/{idUserLevel}', [UserLevelController::class, 'deleteDataUserLevel'])->name('delete');
        });
        Route::prefix('menu/main_menu')->as('main_menu.')->group(function () {
            Route::get('/', [MainMenuController::class, 'viewMainMenu'])->name('view');
            Route::get('/json_data', [MainMenuController::class, 'jsonDataMainMenu'])->name('json_data');
            Route::get('/view/{idMainMenu}', [MainMenuController::class, 'showMainMenuById'])->name('view.id');
            Route::post('/tambah', [MainMenuController::class, 'createDataMainMenu'])->name('create');
            Route::put('/update/{idMainMenu}', [MainMenuController::class, 'updateDataMainMenu'])->name('update');
            Route::delete('/delete/{idMainMenu}', [MainMenuController::class, 'deleteDataMainMenu'])->name('delete');
        });
        Route::prefix('menu/role_akses_menu')->as('role_menu.')->group(function () {
            Route::get('/', [RoleMenuController::class, 'viewRoleMenu'])->name('view');
            Route::get('/json_data/{idUserLevel}', [RoleMenuController::class, 'jsonDataRoleMenu'])->name('json_data');
            Route::get('/form/options', [RoleMenuController::class, 'getFormOptionsRoleMenu'])->name('options');
            Route::get('/form/options/user_level/{idApp}/{idUserLevel}', [RoleMenuController::class, 'getDataRoleSubMenuByIdUserLevel'])->name('options.user_level');
            Route::get('/view/{idUserLevel}/{idRoleMenu}', [RoleMenuController::class, 'showRoleMenuById'])->name('view.id');
            Route::post('/tambah/{idUserLevel}', [RoleMenuController::class, 'createDataRoleMenu'])->name('create');
            Route::put('/update/{idUserLevel}/{idRoleMenu}', [RoleMenuController::class, 'updateDataRoleMenu'])->name('update');
            Route::delete('/delete/{idUserLevel}/{idRoleMenu}', [RoleMenuController::class, 'deleteDataRoleMenu'])->name('delete');
        });
        Route::prefix('users/akun_user')->as('users.akun_user.')->group(function () {
            Route::get('/', [AkunUserController::class, 'viewAkunUser'])->name('view');
            Route::get('/json_data', [AkunUserController::class, 'jsonDataAkunUser'])->name('json_data');
            Route::get('/view/{idUser}', [AkunUserController::class, 'showAkunUserById'])->name('view.id');
            Route::post('/tambah', [AkunUserController::class, 'createDataAkunUser'])->name('create');
            Route::put('/update/{idUser}', [AkunUserController::class, 'updateDataAkunUser'])->name('update');
            Route::delete('/delete/{idUser}', [AkunUserController::class, 'deleteDataAkunUser'])->name('delete');
        });
        Route::prefix('users/user_role')->as('users.user_role.')->group(function () {
            Route::get('/json_data/{idUser}', [AkunUserController::class, 'jsonDataAkunUserRole'])->name('json_data');
            Route::get('/view/{idUserRole}', [AkunUserController::class, 'showAkunUserRoleById'])->name('view.id');
            Route::post('/tambah/{idApp}/{idUser}', [AkunUserController::class, 'createDataAkunUserRole'])->name('create');
            Route::put('/update/{idApp}/{idUserRole}', [AkunUserController::class, 'updateDataAkunUserRole'])->name('update');
            Route::delete('/delete/{idUserRole}', [AkunUserController::class, 'deleteDataAkunUserRole'])->name('delete');
        });
    });

});
