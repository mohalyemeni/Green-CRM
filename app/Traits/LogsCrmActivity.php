<?php

namespace App\Traits;

use App\Models\CrmActivity;
use Illuminate\Database\Eloquent\Model;

trait LogsCrmActivity
{
    protected static function bootLogsCrmActivity()
    {
        static::created(function (Model $model) {
            $model->logActivity('created', "تم إنشاء سجل جديد: " . class_basename($model));
        });

        static::updated(function (Model $model) {
            $changes = $model->getChanges();
            
            // تجاهل تحديثات النظام مثل updated_at
            if (count($changes) === 1 && isset($changes['updated_at'])) return;

            $model->logActivity('updated', "تم تحديث بيانات السجل", $model->getOriginal(), $model->getAttributes());
        });

        static::deleted(function (Model $model) {
            $model->logActivity('deleted', "تم حذف السجل");
        });
    }

    public function activities()
    {
        return $this->morphMany(CrmActivity::class, 'activatable')->latest('created_at');
    }

    public function logActivity(string $action, ?string $description = null, array $oldValues = [], array $newValues = [])
    {
        return $this->activities()->create([
            'action' => $action,
            'description' => $description,
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'user_id' => auth()->id(),
        ]);
    }
}
