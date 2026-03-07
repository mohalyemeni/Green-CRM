<?php

use App\Models\Permission;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Cache;

// pluck('children') : it will get all children under this level
// flatten() : will chage associated array to normal array :mean pring up children to be in the same level with parent
// flatten(): help to look for any element even if it is parent or children they will be in the same level

function getParentShowOf($param){
    $route = str_replace('admin.', '', $param);
    $permission =collect(Cache::get('admin_side_menu')->pluck('children')->flatten())->where('as', $route)->flatten()->first();
    return $permission ? $permission['parent_show'] : null;
}

function getParentOf($param){
    $route = str_replace('admin.', '', $param);
    $permission =collect(Cache::get('admin_side_menu')->pluck('children')->flatten())->where('as', $route)->flatten()->first();
    return $permission ? $permission['parent'] : null;
}

function getParentIdOf($param){
    $route = str_replace('admin.', '', $param);
    $permission = collect(Cache::get('admin_side_menu')->pluck('children')->flatten())->where('as', $route)->flatten()->first();
    return $permission ? $permission['id'] : null;
}



