<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;

class UserLevel extends BaseModel
{
    protected $table = 'user_level'; // nama tabel

    protected $primaryKey = 'id'; // primary key tabel

    protected $keyType = 'int'; // tipe primary key

    protected $fillable = [ // kolom yang boleh dimanipulasi
        'biodata_ref_id',
        'app_id',
        'user_level_name',
        'status',
    ];

    public $timestamps = false; // matikan saja, karena sudah ada bawaan dari databasenya

    public function getUserLevel()
    {
        $query = DB::table("$this->table as a")
                    ->join("$this->t_biodata_ref as b", 'a.biodata_ref_id', '=', 'b.id')
                    ->join("$this->t_app as c", 'a.app_id', '=', 'c.id')
                    ->select([
                        'a.id',
                        'a.user_level_name',
                        'b.biodata_ref_name',
                        'c.app_name',
                        'a.status'
                    ])
                    ->orderBy('c.app_name', 'asc')
                    ->orderBy('a.user_level_name', 'asc')
                    ->get();
        return $query;
    }

    public function getUserLevelStatusTrue($idApp, $idUserLevel = false)
    {
        $query = DB::table("$this->table as a")
            ->select(['a.id', 'a.user_level_name'])
            ->where('a.status', true)
            ->where('a.app_id', $idApp);

        if ($idUserLevel) {
            $query->where(function ($q) use ($idUserLevel) {
                $q->where('a.status', true)
                ->orWhere('a.id', $idUserLevel); // pengecualian untuk yang sedang diedit
            });
        }

        $query->orderBy('a.user_level_name', 'asc');
        return $query->get();
    }

    public function getUserLevelById($idUserLevel)
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id','a.biodata_ref_id', 'a.app_id', 'a.user_level_name', 'a.status'])
                    ->where('a.id', $idUserLevel)
                    ->first();
        return $query;
    }
    public function getUserLevelByIdAppId($idApp, $idUserLevel)
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id','a.biodata_ref_id', 'a.app_id', 'a.user_level_name', 'a.status'])
                    ->where('a.app_id', $idApp)
                    ->where('a.id', $idUserLevel)
                    ->first();
        return $query;
    }

    public function insertUserLevel($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function updateUserLevel($data, $id)
    {
        return DB::table($this->table)->where('id', $id)->update($data);
    }

    public function deleteUserLevel($id)
    {
        return DB::table($this->table)->where('id', $id)->delete();
    }

    // VALIDASI
    public function getUserLevelNameUnique($value, $idUserLevel=false)
    {
        $isUnique = DB::table("$this->table as a")
                    ->select(['a.id'])
                    ->where('a.biodata_ref_id', $value['biodata_ref_id'])
                    ->where('a.app_id', $value['app_id'])
                    ->where('a.user_level_name', $value['user_level_name']);
        if($idUserLevel) {
            $isUnique->where('a.id', '!=', $idUserLevel);
        }
        return $isUnique->exists();
    }
}
