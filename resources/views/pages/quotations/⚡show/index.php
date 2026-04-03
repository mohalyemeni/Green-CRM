<?php

namespace App\Livewire;

use App\Models\Quotation;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('عرض تفاصيل الفاتورة')] class extends Component
{
    public Quotation $quotation;
    public $activities = [];

    public function mount(Quotation $quotation)
    {
        $this->quotation = $quotation->load(['items.service', 'creator', 'activities.user']);
        $this->activities = $quotation->activities()->latest()->get();
    }
};
