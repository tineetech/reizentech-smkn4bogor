<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserRole extends BaseModel
{
    protected $table = 'user_role'; // nama tabel

    protected $primaryKey = 'id'; // primary key tabel

    protected $keyType = 'string'; // tipe primary key

    protected $fillable = [ // kolom yang boleh dimanipulasi
        'user_id',
        'user_level_id',
        'status',
    ];

    public function getUserRoleByUserIdAppName($userId, $appName)
    {
        $query = DB::table("$this->table as a")
                    ->join("$this->t_user_level as b", 'a.user_level_id', '=', 'b.id')
                    ->join("$this->t_biodata_ref as c", 'b.biodata_ref_id', '=', 'c.id')
                    ->join("$this->t_app as d", 'b.app_id', '=', 'd.id')
                    ->select([
                        'a.id',
                        'a.user_level_id',
                        'b.user_level_name',
                        'c.biodata_ref_name',
                        'a.status'
                    ])
                    ->where('a.user_id', $userId)
                    ->where('d.app_name', $appName)
                    ->get();

        return $query;
    }

    public function getUserRoleByIdUser($idUser)
    {
        return DB::table("$this->table as a")
                ->join("$this->t_user_level as b", 'a.user_level_id', '=', 'b.id')
                ->join("$this->t_biodata_ref as c", 'b.biodata_ref_id', '=', 'c.id')
                ->join("$this->t_app as d", 'b.app_id', '=', 'd.id')
                ->select([
                    'a.id',
                    'b.app_id',
                    'd.app_name',
                    'c.biodata_ref_name',
                    'b.user_level_name',
                    'a.status'
                ])
                ->where('a.user_id', $idUser)
                ->get();
    }

    public function getUserRoleById($idUserRole)
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id', 'a.user_id', 'a.user_level_id', 'a.status'])
                    ->where('a.id', $idUserRole)
                    ->first();
        return $query;
    }

    public function insertUserRole($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function updateUserRole($data, $id)
    {
        return DB::table($this->table)->where('id', $id)->update($data);
    }

    public function deleteUserRole($id)
    {
        return DB::table($this->table)->where('id', $id)->delete();
    }

    // VALIDASI
    public function getUserRoleUnique($value, $idUserRole=false)
    {
        $isUnique = DB::table("$this->table as a")
                    ->select(['a.id'])
                    ->where('a.user_id', $value['user_id'])
                    ->where('a.user_level_id', $value['user_level_id']);
        if($idUserRole) {
            $isUnique->where('a.id', '!=', $idUserRole);
        }
        return $isUnique->exists();
    }
}
