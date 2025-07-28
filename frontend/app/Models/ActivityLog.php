<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends BaseModel
{
    protected $table = 'activity_logs'; // nama tabel

    protected $primaryKey = 'id'; // primary key tabel

    protected $keyType = 'string'; // tipe primary key

    protected $fillable = [ // kolom yang boleh dimanipulasi
        'user_role_id',
        'activity',
        'description',
        'ip_address',
        'user_agent',
        'activity_time',
    ];

    public $timestamps = false;
}
