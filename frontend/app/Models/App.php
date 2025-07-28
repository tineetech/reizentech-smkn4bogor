<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;

class App extends BaseModel
{
    protected $table = 'app'; // nama tabel

    protected $primaryKey = 'id'; // primary key tabel

    protected $keyType = 'int'; // tipe primary key

    protected $fillable = [ // kolom yang boleh dimanipulasi
        'app_name',
        'desc',
        'status',
    ];

    public $timestamps = false; // matikan saja, karena sudah ada bawaan dari databasenya

    public function getApp()
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id', 'a.app_name', 'a.desc', 'a.status'])
                    ->orderBy('a.app_name', 'asc')
                    ->get();
        return $query;
    }

    public function getAppStatusTrue($idApp = false)
    {
        $query = DB::table("$this->table as a")
            ->select(['a.id', 'a.app_name'])
            ->where('a.status', true);

        if ($idApp) {
            $query->where(function ($q) use ($idApp) {
                $q->where('a.status', true)
                ->orWhere('a.id', $idApp); // pengecualian untuk yang sedang diedit
            });
        }

        $query->orderBy('a.app_name', 'asc');
        return $query->get();
    }

    public function getAppById($idApp)
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id', 'a.app_name', 'a.desc', 'a.status'])
                    ->where('a.id', $idApp)
                    ->first();
        return $query;
    }

    public function insertApp($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function updateApp($data, $id)
    {
        return DB::table($this->table)->where('id', $id)->update($data);
    }

    public function deleteApp($id)
    {
        return DB::table($this->table)->where('id', $id)->delete();
    }
}
