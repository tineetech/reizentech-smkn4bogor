<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MenuRole extends BaseModel
{
    protected $table = 'menu_role'; // nama tabel

    protected $primaryKey = 'id'; // primary key tabel

    protected $keyType = 'int'; // tipe primary key

    protected $fillable = [ // kolom yang boleh dimanipulasi
        'menu_id',
        'user_level_id',
        'parent_menu_code',
        'menu_icon',
        'menu_url',
        'role_view',
        'role_create',
        'role_update',
        'role_delete',
        'role_order',
        'status',
    ];

    public function getMainMenuByUserLevelId($userLevelId)
    {
        $query = DB::table("$this->table as a")
                    ->join("$this->t_menu as b", 'a.menu_id', '=', 'b.id')
                    ->select(['a.id', 'b.menu_code', 'b.menu_name', 'b.menu_type', 'a.parent_menu_code', 'a.menu_icon', 'a.menu_url', 'a.role_view', 'a.role_create', 'a.role_update', 'a.role_delete', 'a.role_order', 'a.status'])
                    ->where('a.user_level_id', $userLevelId)
                    ->where('a.status', true)
                    ->whereNull('a.parent_menu_code')
                    ->orderBy('a.role_order', 'asc')
                    ->get();
        return $query;
    }

    public function getSubMenuByUserLevelIdMenuCode($userLevelId, $parentMenuCode)
    {
        $query = DB::table("$this->table as a")
                    ->join("$this->t_menu as b", 'a.menu_id', '=', 'b.id')
                    ->select(['a.id', 'b.menu_code', 'b.menu_name', 'b.menu_type', 'a.parent_menu_code', 'a.menu_icon', 'a.menu_url', 'a.role_view', 'a.role_create', 'a.role_update', 'a.role_delete', 'a.role_order', 'a.status'])
                    ->where('a.user_level_id', $userLevelId)
                    ->where('a.parent_menu_code', $parentMenuCode)
                    ->where('a.status', true)
                    ->orderBy('a.role_order', 'asc')
                    ->get();
        return $query;
    }

    public function getMainMenuRoleByUserLevelId($userLevelId)
    {
        $query = DB::table("$this->table as a")
                    ->join("$this->t_menu as b", 'a.menu_id', '=', 'b.id')
                    ->select(['a.id', 'b.menu_code', 'b.menu_name', 'b.menu_type', 'a.parent_menu_code', 'a.menu_icon', 'a.menu_url', 'a.role_view', 'a.role_create', 'a.role_update', 'a.role_delete', 'a.role_order', 'a.status'])
                    ->where('a.user_level_id', $userLevelId)
                    ->whereNull('a.parent_menu_code')
                    ->orderBy('a.role_order', 'asc')
                    ->get();
        return $query;
    }

    public function getSubMenuRoleByUserLevelIdMenuCode($userLevelId, $parentMenuCode)
    {
        $query = DB::table("$this->table as a")
                    ->join("$this->t_menu as b", 'a.menu_id', '=', 'b.id')
                    ->select(['a.id', 'b.menu_code', 'b.menu_name', 'b.menu_type', 'a.parent_menu_code', 'a.menu_icon', 'a.menu_url', 'a.role_view', 'a.role_create', 'a.role_update', 'a.role_delete', 'a.role_order', 'a.status'])
                    ->where('a.user_level_id', $userLevelId)
                    ->where('a.parent_menu_code', $parentMenuCode)
                    ->orderBy('a.role_order', 'asc')
                    ->get();
        return $query;
    }

    public function getMenuRoleById($idUserLevel, $idRoleMenu)
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id', 'a.menu_id', 'a.user_level_id', 'a.parent_menu_code', 'a.menu_icon', 'a.menu_url', 'a.role_view', 'a.role_create', 'a.role_update', 'a.role_delete', 'a.role_order', 'a.status'])
                    ->where('a.user_level_id', $idUserLevel)
                    ->where('a.id', $idRoleMenu)
                    ->first();
        return $query;
    }

    public function insertMenuRole($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function updateMenuRole($data, $id)
    {
        return DB::table($this->table)->where('id', $id)->update($data);
    }

    public function deleteMenuRole($idUserLevel, $idRoleMenu)
    {
        return DB::table($this->table)->where('user_level_id', $idUserLevel)->where('id', $idRoleMenu)->delete();
    }

    public function getAuthMenu($userLevelId, $menuCode, $parentMenuCode)
    {
        $query = DB::table("$this->table as a")
                    ->join("$this->t_menu as b", 'a.menu_id', '=', 'b.id')
                    ->select(['a.role_view', 'a.role_create', 'a.role_update', 'a.role_delete', 'a.role_order'])
                    ->where('a.user_level_id', $userLevelId)
                    ->where('b.menu_code', $menuCode)
                    ->where('a.parent_menu_code', $parentMenuCode)
                    ->where('a.status', true)
                    ->first();
        return $query;
    }

    // VALIDASI
    public function getMenuRoleUnique($value, $idRoleMenu=false)
    {
        $isUnique = DB::table("$this->table as a")
                    ->select(['a.id'])
                    ->where('a.menu_id', $value['menu_id'])
                    ->where('a.user_level_id', $value['user_level_id'])
                    ->where('a.parent_menu_code', $value['parent_menu_code']);
        if($idRoleMenu) {
            $isUnique->where('a.id', '!=', $idRoleMenu);
        }
        return $isUnique->exists();
    }
}
