<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    protected $table = 'menu'; // nama tabel

    protected $primaryKey = 'id'; // primary key tabel

    protected $keyType = 'int'; // tipe primary key

    protected $fillable = [ // kolom yang boleh dimanipulasi
        'menu_code',
        'menu_name',
        'menu_type',
        'status',
    ];

    public $timestamps = false; // matikan saja, karena sudah ada bawaan dari databasenya

    public function getMenu()
    {
        $query = DB::table("$this->table as a")
                    ->select([
                        'a.id',
                        'a.menu_code',
                        'a.menu_name',
                        'a.menu_type',
                        'a.status'
                    ])
                    ->orderBy('a.menu_name', 'asc')
                    ->get();
        return $query;
    }

    public function getMenuStatusTrue($idMenu = false)
    {
        $query = DB::table("$this->table as a")
            ->select(['a.id', 'a.menu_code', 'a.menu_name'])
            ->where('a.status', true);

        if ($idMenu) {
            $query->where(function ($q) use ($idMenu) {
                $q->where('a.status', true)
                ->orWhere('a.id', $idMenu); // pengecualian untuk yang sedang diedit
            });
        }

        $query->orderBy('a.menu_name', 'asc');
        return $query->get();
    }

    public function getParentMenuStatusTrue($parentMenuCode = false)
    {
        $query = DB::table("$this->table as a")
            ->select(['a.id', 'a.menu_code', 'a.menu_name'])
            ->where('a.status', true)
            ->where('a.menu_type', 'sub');

        if ($parentMenuCode) {
            $query->where(function ($q) use ($parentMenuCode) {
                $q->where('a.status', true)
                ->orWhere('a.menu_code', $parentMenuCode); // pengecualian untuk yang sedang diedit
            });
        }

        $query->orderBy('a.menu_name', 'asc');
        return $query->get();
    }

    public function getMenuById($idMenu)
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id','a.menu_code', 'a.menu_name', 'a.menu_type', 'a.status'])
                    ->where('a.id', $idMenu)
                    ->first();
        return $query;
    }

    public function insertMenu($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function updateMenu($data, $id)
    {
        return DB::table($this->table)->where('id', $id)->update($data);
    }

    public function deleteMenu($id)
    {
        return DB::table($this->table)->where('id', $id)->delete();
    }
}
