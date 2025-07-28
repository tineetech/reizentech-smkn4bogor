<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class User extends BaseModel
{

    protected $table = 'users'; // nama tabel

    protected $primaryKey = 'id'; // primary key tabel

    protected $keyType = 'string'; // tipe primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function authWithUsername($username)
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id', 'a.username', 'a.password', 'a.status'])
                    ->where('a.username', $username)
                    ->first();
        return $query;
    }

    public function getUsers()
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Subquery PostgreSQL
            $subquery = DB::table("$this->t_user_role as b")
                ->join("$this->t_user_level as c", 'b.user_level_id', '=', 'c.id')
                ->join("$this->t_biodata_ref as d", 'c.biodata_ref_id', '=', 'd.id')
                ->join("$this->t_app as e", 'c.app_id', '=', 'e.id')
                ->selectRaw("
                    b.user_id,
                    e.app_name,
                    STRING_AGG(DISTINCT d.biodata_ref_name || ' - ' || c.user_level_name, ', ' ORDER BY d.biodata_ref_name) as level_roles
                ")
                ->groupBy('b.user_id', 'e.app_name');

            $rolesConcat = DB::table(DB::raw("({$subquery->toSql()}) as x"))
                ->mergeBindings($subquery)
                ->selectRaw("
                    x.user_id,
                    STRING_AGG(x.app_name || ': ' || x.level_roles, ' | ') as roles
                ")
                ->groupBy('x.user_id');
        } else {
            // Subquery MariaDB
            $subquery = DB::table("$this->t_user_role as b")
                ->join("$this->t_user_level as c", 'b.user_level_id', '=', 'c.id')
                ->join("$this->t_biodata_ref as d", 'c.biodata_ref_id', '=', 'd.id')
                ->join("$this->t_app as e", 'c.app_id', '=', 'e.id')
                ->selectRaw("
                    b.user_id,
                    e.app_name,
                    GROUP_CONCAT(DISTINCT CONCAT(d.biodata_ref_name, ' - ', c.user_level_name) ORDER BY d.biodata_ref_name SEPARATOR ', ') as level_roles
                ")
                ->groupBy('b.user_id', 'e.app_name');

            $rolesConcat = DB::table(DB::raw("({$subquery->toSql()}) as x"))
                ->mergeBindings($subquery)
                ->selectRaw("
                    x.user_id,
                    GROUP_CONCAT(CONCAT(x.app_name, ': ', x.level_roles) SEPARATOR ' | ') as roles
                ")
                ->groupBy('x.user_id');
        }

        // Query utama user + roles
        $query = DB::table("$this->table as a")
            ->leftJoinSub($rolesConcat, 'r', 'a.id', '=', 'r.user_id')
            ->select([
                'a.id',
                'a.username',
                'a.email',
                'a.phone_number',
                'a.status',
                'r.roles',
            ])
            ->orderBy('a.username', 'asc')
            ->get();

        return $query;
    }



    public function getUsersById($idUser)
    {
        $query = DB::table("$this->table as a")
                    ->select(['a.id', 'a.username', 'a.email', 'a.phone_number', 'a.status'])
                    ->where('a.id', $idUser)
                    ->first();
        return $query;
    }

    public function insertUsers($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function updateUsers($data, $id)
    {
        return DB::table($this->table)->where('id', $id)->update($data);
    }

    public function deleteUsers($id)
    {
        return DB::table($this->table)->where('id', $id)->delete();
    }
}
