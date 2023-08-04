<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentMenu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'module_id',
        'parent_menu_name',
        'parent_menu_icon',
        'parent_menu_url',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
