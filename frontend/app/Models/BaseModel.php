<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BaseModel extends Model
{
    protected $t_users = 'users';
    protected $t_app = 'app';
    protected $t_biodata_ref = 'biodata_ref';
    protected $t_user_biodata = 'user_biodata';
    protected $t_user_role = 'user_role';
    protected $t_user_level = 'user_level';
    protected $t_menu = 'menu';
    protected $t_menu_role = 'menu_role';
    protected $t_activity_logs = 'activity_logs';
}
