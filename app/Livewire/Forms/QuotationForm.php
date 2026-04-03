<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Enums\QuotationStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuotationForm extends Form
{
    public ?Quotation $quotation = null;

    // === البيانات الأساسية ===
    public ?string $code = null;
    public string $title = 'عرض سعر';
    public ?string $issue_date = null;
    public ?string $expiry_date = null;

    // === بيانات العميل ===
    public ?int $customer_id = null;
    public ?string $customer_name = null;
    public ?string $customer_email = null;
    public ?string $customer_phone = null;
    public ?string $customer_address = null;

    // === البنود (Items) ===
    public array $items = [];

    // === الإجماليات (Totals) ===
    public float $subtotal = 0;
    public float $discount_amount = 0;
    public string $discount_type = 'amount';
    public float $tax_amount = 0;
    public float $total = 0;

    // === أخرى ===
    public string $status = 'draft';
    public ?string $notes = null;
    public ?string $terms_conditions = null;
    public ?string $customer_notes = null;

    public function mount()
    {
        $this->issue_date = date('Y-m-d');
        $this->expiry_date = date('Y-m-d', strtotime('+30 days'));
        $this->addItem(); // يبدأ العرض ببند فارغ على الأقل
    }

    public function rules(): array
    {
        return [
            'title'               => 'required|string|max:255',
            'issue_date'          => 'required|date',
            'expiry_date'         => 'required|date|after_or_equal:issue_date',
            
            'customer_id'         => 'nullable|exists:customers,id',
            'customer_name'       => 'required_without:customer_id|nullable|string|max:255',
            'customer_email'      => 'nullable|email|max:255',
            'customer_phone'      => 'nullable|string|max:20',
            
            'items'               => 'required|array|min:1',
            'items.*.item_name'   => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
            
            'discount_amount'     => 'nullable|numeric|min:0',
            'discount_type'       => 'required|in:amount,percentage',
        ];
    }

    // إضافة بند جديد
    public function addItem()
    {
        $this->items[] = [
            'service_id'      => null,
            'item_name'       => '',
            'description'     => '',
            'quantity'        => 1,
            'unit_price'      => 0,
            'discount_amount' => 0,
            'discount_type'   => 'amount',
            'is_taxable'      => true,
            'tax_rate'        => 15.00, // يمكن تعديلها للافتراضي حسب النظام
            'tax_amount'      => 0,
            'subtotal'        => 0,
            'total'           => 0,
        ];
        $this->calculateTotals();
    }

    // إزالة بند
    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // لإعادة ترتيب الـ index
        $this->calculateTotals();
    }

    // الحسابات الرياضية للعرض والبنود
    public function calculateTotals()
    {
        $subtotal = 0;
        $totalTax = 0;

        foreach ($this->items as $index => $item) {
            $qty = (float)($item['quantity'] ?? 0);
            $price = (float)($item['unit_price'] ?? 0);
            $itemSubtotal = $qty * $price;

            // الخصم على البند
            $itemDiscount = 0;
            $dscVal = (float)($item['discount_amount'] ?? 0);
            if ($item['discount_type'] === 'percentage') {
                $itemDiscount = $itemSubtotal * ($dscVal / 100);
            } else {
                $itemDiscount = $dscVal;
            }

            $itemSubtotalAfterDiscount = max(0, $itemSubtotal - $itemDiscount);

            // الضريبة على البند
            $itemTax = 0;
            if (isset($item['is_taxable']) && $item['is_taxable']) {
                $taxRate = (float)($item['tax_rate'] ?? 0);
                $itemTax = $itemSubtotalAfterDiscount * ($taxRate / 100);
            }

            $itemTotal = $itemSubtotalAfterDiscount + $itemTax;

            // تحديث قيم البند في الـ Array
            $this->items[$index]['subtotal'] = $itemSubtotalAfterDiscount;
            $this->items[$index]['tax_amount'] = $itemTax;
            $this->items[$index]['total'] = $itemTotal;

            $subtotal += $itemSubtotalAfterDiscount;
            $totalTax += $itemTax;
        }

        $this->subtotal = $subtotal;
        $this->tax_amount = $totalTax;

        // الخصم العام على الفاتورة (إن وجد)
        $globalDiscount = 0;
        $glDscVal = (float)$this->discount_amount;
        if ($this->discount_type === 'percentage') {
            $globalDiscount = $this->subtotal * ($glDscVal / 100);
        } else {
            $globalDiscount = $glDscVal;
        }

        $totalAfterGlobalDiscount = max(0, $this->subtotal - $globalDiscount);
        $this->total = $totalAfterGlobalDiscount + $this->tax_amount;
    }

    private function generateCode()
    {
        $year = date('Y');
        $lastQuotation = Quotation::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastQuotation && preg_match('/QT-' . $year . '-(\d+)/', $lastQuotation->code, $matches)) {
            $sequence = (int)$matches[1] + 1;
        } else {
            $sequence = 1;
        }

        return 'QT-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function setQuotation(Quotation $quotation)
    {
        $this->quotation = $quotation;
        $this->code = $quotation->code;
        $this->title = $quotation->title;
        $this->issue_date = $quotation->issue_date ? Carbon::parse($quotation->issue_date)->format('Y-m-d') : null;
        $this->expiry_date = $quotation->expiry_date ? Carbon::parse($quotation->expiry_date)->format('Y-m-d') : null;

        $this->customer_id = $quotation->customer_id;
        $this->customer_name = $quotation->customer_name;
        $this->customer_email = $quotation->customer_email;
        $this->customer_phone = $quotation->customer_phone;
        $this->customer_address = $quotation->customer_address;

        $this->subtotal = (float)$quotation->subtotal;
        $this->discount_amount = (float)$quotation->discount_amount;
        $this->discount_type = $quotation->discount_type;
        $this->tax_amount = (float)$quotation->tax_amount;
        $this->total = (float)$quotation->total;

        $this->status = $quotation->status->value ?? 'draft';
        $this->notes = $quotation->notes;
        $this->terms_conditions = $quotation->terms_conditions;
        $this->customer_notes = $quotation->customer_notes;

        $this->items = $quotation->items->toArray();
    }

    public function save()
    {
        $this->validate();
        $this->calculateTotals(); // تأكيد مراجعة الحسابات قبل الحفظ

        DB::transaction(function () {
            $isCreate = is_null($this->quotation);

            $data = [
                'title'            => $this->title,
                'issue_date'       => $this->issue_date,
                'expiry_date'      => $this->expiry_date,
                'customer_id'      => $this->customer_id,
                'customer_name'    => $this->customer_name,
                'customer_email'   => $this->customer_email,
                'customer_phone'   => $this->customer_phone,
                'customer_address' => $this->customer_address,
                'subtotal'         => $this->subtotal,
                'discount_amount'  => $this->discount_amount ?: 0,
                'discount_type'    => $this->discount_type,
                'tax_amount'       => $this->tax_amount,
                'total'            => $this->total,
                'status'           => $this->status,
                'notes'            => $this->notes,
                'terms_conditions' => $this->terms_conditions,
                'customer_notes'   => $this->customer_notes,
            ];

            if ($isCreate) {
                $data['code'] = $this->code ?? $this->generateCode();
                $data['created_by'] = auth()->id();
                $this->quotation = Quotation::create($data);

                // حفظ النشاط
                $this->logActivity('created', 'تم إنشاء عرض السعر');
            } else {
                $data['updated_by'] = auth()->id();
                $oldData = $this->quotation->toArray();
                $this->quotation->update($data);

                // حفظ النشاط (مع الاحتفاظ بالتغيرات)
                $this->logActivity('updated', 'تم تحديث بيانات عرض السعر', $oldData, $data);
                
                // مسح البنود القديمة لإعادة تشكيلها (أو يمكن التحديث إذا أردت تعقيداً أقل للمسح الكامل)
                $this->quotation->items()->delete();
            }

            // حفظ البنود الجديدة
            $sort_order = 1;
            foreach ($this->items as $item) {
                $this->quotation->items()->create([
                    'service_id'      => $item['service_id'] ?: null,
                    'item_name'       => $item['item_name'],
                    'description'     => $item['description'] ?? null,
                    'quantity'        => $item['quantity'],
                    'unit_price'      => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'discount_type'   => $item['discount_type'] ?? 'amount',
                    'is_taxable'      => filter_var($item['is_taxable'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'tax_rate'        => $item['tax_rate'] ?? 0,
                    'tax_amount'      => $item['tax_amount'] ?? 0,
                    'subtotal'        => $item['subtotal'] ?? 0,
                    'total'           => $item['total'] ?? 0,
                    'sort_order'      => $sort_order++,
                ]);
            }
        });

        $this->reset();
        $this->mount();
        return $this->quotation;
    }

    private function logActivity($action, $description, $oldValues = null, $newValues = null)
    {
        $this->quotation->activities()->create([
            'action'      => $action,
            'description' => $description,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'user_id'     => auth()->id(),
        ]);
    }
}
