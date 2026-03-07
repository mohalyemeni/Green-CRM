<?php

namespace App\Models;

use Mindscms\Entrust\EntrustRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends EntrustRole
{
    use HasFactory;

    protected $guarded = [];
}
