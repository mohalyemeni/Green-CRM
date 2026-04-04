<?php

namespace App\Livewire\Pages\Leads;

use Livewire\Component;
use App\Livewire\Forms\LeadForm;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use App\Enums\ActiveStatus;
use App\Enums\PriorityLevel;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

new #[Title('إضافة عميل محتمل')] class extends Component
{
    public LeadForm $form;

    public function save()
    {
        $this->form->store();
        $this->dispatch('notify', type: 'success', message: 'تم إضافة العميل المحتمل بنجاح!');
        return redirect()->route('admin.leads.index');
    }

    #[Computed]
    public function sources()
    {
        return LeadSource::where('status', ActiveStatus::ACTIVE)->orderBy('name')->get();
    }

    #[Computed]
    public function statuses()
    {
        return LeadStatus::where('status', ActiveStatus::ACTIVE->value)->orderBy('sort_order')->get();
    }

    #[Computed]
    public function users()
    {
        return User::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
    }

    #[Computed]
    public function priorities()
    {
        return PriorityLevel::cases();
    }

    public function render()
    {
        return view('pages.leads.⚡create.create');
    }
};
