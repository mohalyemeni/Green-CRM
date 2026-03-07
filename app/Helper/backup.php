<?php

use App\Models\Permission;
use Illuminate\Support\Facades\Cache;


function getParentShowOf($param){
    $route = str_replace('admin.', '', $param);
    $permission =collect(Cache::get('admin_side_menu')->pluck('children')->flatten())->where('as', $route)->flatten()->first();
    return $permission ? $permission['parent_show'] : null;
}

function getParentOf($param){
    $route = str_replace('admin.', '', $param);
    $permission = Cache::get('admin_side_menu')->where('as', $route)->first();
    return $permission ? $permission->parent : $route;
}

function getParentIdOf($param){
    $route = str_replace('admin.', '', $param);
    $permission = Cache::get('admin_side_menu')->where('as', $route)->first();
    return $permission ? $permission->id : null;
}

function getCurrentAS($param){
    $route = str_replace('admin.', '', $param);
    $permission =collect(Cache::get('admin_side_menu')->pluck('children')->flatten())->where('as', $route)->flatten()->first();
    return $permission ? $permission['as'] : null;
}

// function getTest($param){
//     $route = str_replace('admin.', '', $param);
//     $permission=null;

//     foreach (Cache::get('admin_side_menu') as $menu) {
//         if ( count($menu->appearedChildren) == 0) {
//             $permission = $menu->where('as',$route)->first();
           
//         }
//         elseif($menu->appearedChildren !== null && count($menu->appearedChildren) > 0){
//             foreach ($menu->appearedChildren as $sub_menu) {
//                 $permission = $sub_menu->where('as',$route)->first();
//                 // dd($menu->appearedChildren);
//             }
//         }
//         if($permission != null){
//             $permissiont = $permission->parent;
//         }else{
//             $permissiont =$route;
//         }

//         return $permissiont;
//     } 
    
    
// }

