<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PipelineStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'name' => 'فرصة جديدة',
                'name_en' => 'New Lead',
                'code' => 'new_lead',
                'description' => 'New incoming leads that have not been contacted yet',
                'probability' => 10,
                'sort_order' => 1,
                'color' => '#6366f1',
                'is_won' => false,
                'is_lost' => false,
                'status' => \App\Enums\ActiveStatus::ACTIVE->value,
            ],
            [
                'name' => 'تم التواصل',
                'name_en' => 'Contacted',
                'code' => 'contacted',
                'description' => 'Initial contact has been made with the lead',
                'probability' => 25,
                'sort_order' => 2,
                'color' => '#f59e0b',
                'is_won' => false,
                'is_lost' => false,
                'status' => \App\Enums\ActiveStatus::ACTIVE->value,
            ],
            [
                'name' => 'تم التأهيل',
                'name_en' => 'Qualified',
                'code' => 'qualified',
                'description' => 'Lead has been qualified and meets basic criteria',
                'probability' => 50,
                'sort_order' => 3,
                'color' => '#10b981',
                'is_won' => false,
                'is_lost' => false,
                'status' => \App\Enums\ActiveStatus::ACTIVE->value,
            ],
            [
                'name' => 'تم إرسال العرض',
                'name_en' => 'Proposal Sent',
                'code' => 'proposal_sent',
                'description' => 'A proposal has been sent to the qualified lead',
                'probability' => 75,
                'sort_order' => 4,
                'color' => '#3b82f6',
                'is_won' => false,
                'is_lost' => false,
                'status' => \App\Enums\ActiveStatus::ACTIVE->value,
            ],
            [
                'name' => 'قيد التفاوض',
                'name_en' => 'Negotiation',
                'code' => 'negotiation',
                'description' => 'Negotiations are in progress',
                'probability' => 90,
                'sort_order' => 5,
                'color' => '#8b5cf6',
                'is_won' => false,
                'is_lost' => false,
                'status' => \App\Enums\ActiveStatus::ACTIVE->value,
            ],
            [
                'name' => 'مغلقة - ناجحة',
                'name_en' => 'Won',
                'code' => 'won',
                'description' => 'Deal has been won and closed',
                'probability' => 100,
                'sort_order' => 6,
                'color' => '#10b981',
                'is_won' => true,
                'is_lost' => false,
                'status' => \App\Enums\ActiveStatus::ACTIVE->value,
            ],
            [
                'name' => 'مغلقة - خاسرة',
                'name_en' => 'Lost',
                'code' => 'lost',
                'description' => 'Deal has been lost',
                'probability' => 0,
                'sort_order' => 7,
                'color' => '#ef4444',
                'is_won' => false,
                'is_lost' => true,
                'status' => \App\Enums\ActiveStatus::ACTIVE->value,
            ],
        ];

        foreach ($stages as $stage) {
            \App\Models\PipelineStage::create([
                ...$stage,
                'created_by' => 1,  // ← يفضل auth()->id() إذا كان Seeder مخصص
            ]);
        }
    }
}
