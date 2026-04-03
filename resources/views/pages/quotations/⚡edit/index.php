<?php

namespace App\Livewire;

use App\Livewire\Forms\QuotationForm;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Quotation;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('تعديل عرض سعر')] class extends Component
{
    public Quotation $qModel;
    public QuotationForm $form;

    public function mount(Quotation $quotation)
    {
        $this->qModel = $quotation;
        $this->form->setQuotation($quotation);
    }

    public function getActiveCustomersProperty()
    {
        return Customer::select('id', 'name', 'email', 'phone', 'address')
            ->orderBy('name')
            ->get();
    }

    public function getActiveServicesProperty()
    {
        return Service::where('status', 1)
            ->orderBy('name')
            ->select('id', 'name', 'description', 'price', 'is_taxable', 'tax_rate', 'max_discount')
            ->get();
    }

    public function updatedFormCustomerId($value)
    {
        if ($value) {
            $customer = Customer::find($value);
            if ($customer) {
                $this->form->customer_name = $customer->name;
                $this->form->customer_email = $customer->email;
                $this->form->customer_phone = $customer->phone;
                $this->form->customer_address = $customer->address;
            }
        } else {
            $this->form->customer_name = null;
            $this->form->customer_email = null;
            $this->form->customer_phone = null;
            $this->form->customer_address = null;
        }
    }

    public function selectServiceFor($index, $serviceId)
    {
        if (!$serviceId) return;

        $service = Service::find($serviceId);
        if ($service) {
            $this->form->items[$index]['item_name'] = $service->name;
            $this->form->items[$index]['description'] = $service->description;
            $this->form->items[$index]['unit_price'] = (float)$service->price;
            $this->form->items[$index]['is_taxable'] = (bool)$service->is_taxable;
            $this->form->items[$index]['tax_rate'] = (float)$service->tax_rate;
            
            $this->form->calculateTotals();
        }
    }

    public function addItem()
    {
        $this->form->addItem();
    }

    public function removeItem($index)
    {
        $this->form->removeItem($index);
    }

    public function recalculate()
    {
        $this->form->calculateTotals();
    }

    public function save()
    {
        $this->form->save();
        session()->flash('notify', ['type' => 'success', 'message' => 'تم تحديث عرض السعر بنجاح.']);
        return redirect()->route('admin.quotations.index');
    }
};
