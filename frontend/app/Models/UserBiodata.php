<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserBiodata extends BaseModel
{
    protected $table = 'user_biodata'; // nama tabel

    protected $primaryKey = 'id'; // primary key tabel

    protected $keyType = 'string'; // tipe primary key

    protected $fillable = [ // kolom yang boleh dimanipulasi
        'user_id',
        'user_code',
        'name',
        'sex',
        'date_of_birth',
        'status',
    ];

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

    public function getUserBiodataByUserId($userId)
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id', 'a.user_code', 'a.name', 'a.sex', 'a.date_of_birth', 'a.status'])
                    ->where('a.user_id', $userId)
                    ->first();
        return $query;
    }
}
