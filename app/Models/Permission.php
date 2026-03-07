<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mindscms\Entrust\EntrustPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends EntrustPermission
{
    use HasFactory;

    protected $guarded = [];

    public function parent()
    {
        return $this->hasOne(Permission::class, 'id', 'parent');
    }

    public function children()
    {
        return $this->hasMany(Permission::class, 'parent', 'id');
    }


    // تعيد الابناء للاب الذي يكون فيه الابناء قيمة الابيرد تساوي 1 اي ظاهرين
    public function appearedChildren()
    {
        return $this->hasMany(Permission::class, 'parent', 'id')->where('appear',1);
    }

// هذه الدالة ستعيد كل الصلاحيات الرئيسية و الصلاحيات المتفرعة من كل صلاحية رئيسية
// معني الكلام اعمل استعلام عن كل الصلاحيات الرئيسية التي يكون الاب ليديها 0 وتكون ضاهرة وايضا ستستخدم علي ال سايد بار
// بعد ان توجدها قم بترتيبها تصاعديا ومن ثم قم بايجاد ما اذا كان هناك ابناء للصلاحيات الرئيسية
// وذلك عبر الدالة children و استخدام المتغيرات اي دي و بارينت كما في العلاقة
//  استعرض الصلاحيات الابناء تحت الصلاحيات الاب كعلاقة
    public static function tree( $level = 1 )
    {
        //it will return the only father as tree
        return static::with(implode('.', array_fill(0, $level, 'children')))
            ->whereParent(0)
            ->whereAppear(1)
            ->whereSidebarLink(1)
            ->orderBy('ordering', 'asc')
            ->get();
    }
//استرجاع كافة الصلاحيات للمستخدم والغير موجودة في الـ الدور المحدد له
    public static function assigned_childern( $level = 1 )
    {
        return static::with(implode('.', array_fill(0, $level, 'assigned_childern')))
            ->whereParentOriginal(0)
            ->whereAppear(1)
            ->orderBy('ordering', 'asc')
            ->get();
    }

}
