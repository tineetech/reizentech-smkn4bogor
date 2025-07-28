<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;

class BiodataRef extends BaseModel
{
    protected $table = 'biodata_ref'; // nama tabel

    protected $primaryKey = 'id'; // primary key tabel

    protected $keyType = 'int'; // tipe primary key

    protected $fillable = [ // kolom yang boleh dimanipulasi
        'biodata_ref_name',
        'status',
    ];

    public $timestamps = false; // matikan saja, karena sudah ada bawaan dari databasenya

    public function getBiodataRef()
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id', 'a.biodata_ref_name', 'a.status'])
                    ->orderBy('a.biodata_ref_name', 'asc')
                    ->get();
        return $query;
    }

    public function getBiodataRefStatusTrue($idBiodataRef = false)
    {
        $query = DB::table("$this->table as a")
            ->select(['a.id', 'a.biodata_ref_name'])
            ->where('a.status', true);

        if ($idBiodataRef) {
            $query->where(function ($q) use ($idBiodataRef) {
                $q->where('a.status', true)
                ->orWhere('a.id', $idBiodataRef); // pengecualian untuk yang sedang diedit
            });
        }

        $query->orderBy('a.biodata_ref_name', 'asc');
        return $query->get();
    }

    public function getBiodataRefById($idBiodataRef)
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id','a.biodata_ref_name', 'a.status'])
                    ->where('a.id', $idBiodataRef)
                    ->first();
        return $query;
    }

    public function insertBiodataRef($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function updateBiodataRef($data, $id)
    {
        return DB::table($this->table)->where('id', $id)->update($data);
    }

    public function deleteBiodataRef($id)
    {
        return DB::table($this->table)->where('id', $id)->delete();
    }
}
