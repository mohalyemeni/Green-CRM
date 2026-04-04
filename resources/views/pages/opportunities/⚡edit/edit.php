<?php

namespace App\Livewire\Pages\Opportunities;

use Livewire\Component;
use App\Livewire\Forms\OpportunityForm;
use App\Models\Opportunity;
use App\Models\OpportunitySource;
use App\Models\PipelineStage;
use App\Models\LostReason;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

new #[Title('تعديل الفرصة البيعية')] class extends Component
{
    public OpportunityForm $form;
    public Opportunity $opportunity;

    public function mount(Opportunity $opportunity)
    {
        $this->opportunity = $opportunity;
        $this->form->setOpportunity($opportunity);
    }

    public function save()
    {
        $this->form->update();
        $this->dispatch('notify', type: 'success', message: 'تم تحديث بيانات الفرصة البيعية بنجاح!');
        return redirect()->route('admin.opportunities.show', $this->opportunity->id);
    }

    #[Computed]
    public function sources()
    {
        return OpportunitySource::where('status', ActiveStatus::ACTIVE)->orderBy('name')->get();
    }

    #[Computed]
    public function stages()
    {
        return PipelineStage::where('status', ActiveStatus::ACTIVE)->orderBy('sort_order')->get();
    }

    #[Computed]
    public function lostReasons()
    {
        return LostReason::where('status', ActiveStatus::ACTIVE)->orderBy('sort_order')->get();
    }

    #[Computed]
    public function customers()
    {
        return Customer::orderBy('name')->get(['id', 'name', 'mobile']);
    }

    #[Computed]
    public function companies()
    {
        return Company::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function currencies()
    {
        return Currency::orderBy('name')->get(['id', 'name', 'code']);
    }

    #[Computed]
    public function users()
    {
        return User::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
    }

    #[Computed]
    public function priorities()
    {
        return [
            ['value' => 'low',    'label' => 'منخفضة', 'color' => 'info'],
            ['value' => 'medium', 'label' => 'متوسطة', 'color' => 'primary'],
            ['value' => 'high',   'label' => 'عالية',  'color' => 'warning'],
            ['value' => 'urgent', 'label' => 'عاجلة',  'color' => 'danger'],
        ];
    }

    public function render()
    {
        return view('pages.opportunities.⚡edit.edit');
    }
};
