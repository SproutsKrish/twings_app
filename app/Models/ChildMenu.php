<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChildMenu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'parent_menu_id',
        'child_menu_name',
        'child_menu_icon',
        'child_menu_url',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
