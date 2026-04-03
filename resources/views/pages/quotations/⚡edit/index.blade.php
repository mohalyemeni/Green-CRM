<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">تعديل عرض السعر {{ $qModel->code }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.quotations.index') }}">عروض الأسعار</a></li>
                        <li class="breadcrumb-item active">تعديل</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-xl-9">
                {{-- المستند و التواريخ --}}
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="title" class="form-label fw-bold">عنوان العرض <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('form.title') is-invalid @enderror" id="title" wire:model.blur="form.title">
                                @error('form.title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-lg-3">
                                <label for="issue_date" class="form-label fw-bold">تاريخ الإصدار <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('form.issue_date') is-invalid @enderror" id="issue_date" wire:model.blur="form.issue_date">
                                @error('form.issue_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-lg-3">
                                <label for="expiry_date" class="form-label fw-bold">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('form.expiry_date') is-invalid @enderror" id="expiry_date" wire:model.blur="form.expiry_date">
                                @error('form.expiry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- البنود Items --}}
                <div class="card">
                    <div class="card-header bg-light border-bottom border-light">
                        <h5 class="card-title mb-0"><i class="ri-list-check align-middle me-1"></i> بنود عرض السعر</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 300px;">الخدمة / الوصف</th>
                                        <th style="width: 100px;">الكمية</th>
                                        <th style="width: 130px;">سعر الوحدة</th>
                                        <th style="width: 130px;">الخصم</th>
                                        <th style="width: 80px;">الضريبة</th>
                                        <th style="width: 120px;" class="text-end">الإجمالي</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($form->items as $index => $item)
                                    <tr>
                                        <td>
                                            <select class="form-select form-select-sm mb-2" wire:model.live="form.items.{{ $index }}.service_id" wire:change="selectServiceFor({{ $index }}, $event.target.value)">
                                                <option value="">-- اختر خدمة --</option>
                                                @foreach($this->activeServices as $srv)
                                                <option value="{{ $srv->id }}">{{ $srv->name }} ({{ number_format($srv->price, 2) }})</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control form-control-sm mb-1 @error('form.items.'.$index.'.item_name') is-invalid @enderror" wire:model.blur="form.items.{{ $index }}.item_name" placeholder="اسم البند *">
                                            @error('form.items.'.$index.'.item_name') <small class="text-danger">{{ $message }}</small> @enderror
                                            
                                            <textarea class="form-control form-control-sm" rows="1" wire:model.blur="form.items.{{ $index }}.description" placeholder="وصف إضافي"></textarea>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="1" class="form-control form-control-sm @error('form.items.'.$index.'.quantity') is-invalid @enderror" wire:model.blur="form.items.{{ $index }}.quantity" wire:change="recalculate">
                                            @error('form.items.'.$index.'.quantity') <small class="text-danger">{{ $message }}</small> @enderror
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm @error('form.items.'.$index.'.unit_price') is-invalid @enderror" wire:model.blur="form.items.{{ $index }}.unit_price" wire:change="recalculate">
                                            @error('form.items.'.$index.'.unit_price') <small class="text-danger">{{ $message }}</small> @enderror
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.01" min="0" class="form-control" wire:model.blur="form.items.{{ $index }}.discount_amount" wire:change="recalculate">
                                                <select class="form-select" wire:model.live="form.items.{{ $index }}.discount_type" wire:change="recalculate" style="width: 50px; flex: none; padding: 0.25rem;">
                                                    <option value="amount">$</option>
                                                    <option value="percentage">%</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch mb-1 d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" role="switch" wire:model.live="form.items.{{ $index }}.is_taxable" wire:change="recalculate">
                                            </div>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center" wire:model.blur="form.items.{{ $index }}.tax_rate" wire:change="recalculate" @if(!isset($form->items[$index]['is_taxable']) || !$form->items[$index]['is_taxable']) disabled @endif>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            {{ number_format((float)($form->items[$index]['total'] ?? 0), 2) }}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-soft-danger" wire:click="removeItem({{ $index }})" @if(count($form->items) <= 1) disabled @endif>
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-soft-success btn-sm" wire:click="addItem">
                                <i class="ri-add-line align-middle me-1"></i> أضف بنداً آخر
                            </button>
                            @error('form.items') <span class="text-danger ms-2 fw-medium fs-12">{{ $message }}</span> @enderror
                        </div>

                        {{-- الإجماليات --}}
                        <div class="row justify-content-end mt-4">
                            <div class="col-lg-5">
                                <table class="table table-borderless table-sm fw-medium mb-0">
                                    <tbody>
                                        <tr>
                                            <td>المجموع الفرعي:</td>
                                            <td class="text-end">{{ number_format($form->subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="d-flex align-items-center">
                                                <span class="me-2">الخصم الإضافي:</span>
                                                <div class="input-group input-group-sm" style="width: 140px;">
                                                    <input type="number" step="0.01" min="0" class="form-control" wire:model.blur="form.discount_amount" wire:change="recalculate">
                                                    <select class="form-select" wire:model.live="form.discount_type" wire:change="recalculate" style="width: 50px; flex: none;">
                                                        <option value="amount">$</option>
                                                        <option value="percentage">%</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="text-end text-danger">
                                                - {{ number_format($form->discount_type === 'percentage' ? ($form->subtotal * ($form->discount_amount / 100)) : $form->discount_amount, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>الضريبة المضافة:</td>
                                            <td class="text-end">{{ number_format($form->tax_amount, 2) }}</td>
                                        </tr>
                                        <tr class="border-top border-top-dashed fs-15">
                                            <th scope="row">الإجمالي النهائي:</th>
                                            <th class="text-end">{{ number_format($form->total, 2) }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- الشروط والملاحظات --}}
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">الشروط والأحكام</label>
                                <textarea class="form-control" rows="3" wire:model="form.terms_conditions"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ملاحظات داخلية (لا تظهر للعميل)</label>
                                <textarea class="form-control" rows="3" wire:model="form.notes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                {{-- العميل --}}
                <div class="card">
                    <div class="card-header bg-light border-bottom border-light">
                        <h5 class="card-title mb-0"><i class="ri-user-3-line align-middle me-1"></i> معلومات العميل</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" wire:ignore>
                            <label class="form-label fw-bold">اختر من العملاء المسجلين</label>
                            <select class="form-select" wire:model.live="form.customer_id" id="customerSelect">
                                <option value="">-- اختر عميل أو أدخل يدوياً --</option>
                                @foreach($this->activeCustomers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone ?? '--' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم العميل (إلزامي إذا لم تختر عميلاً مسجلاً)</label>
                            <input type="text" class="form-control @error('form.customer_name') is-invalid @enderror" wire:model="form.customer_name">
                            @error('form.customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control @error('form.customer_email') is-invalid @enderror" wire:model="form.customer_email">
                            @error('form.customer_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control" wire:model="form.customer_phone">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">العنوان</label>
                            <textarea class="form-control" rows="2" wire:model="form.customer_address"></textarea>
                        </div>
                    </div>
                </div>

                {{-- الحفظ --}}
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">تحديث الحالة</label>
                            <select class="form-select" wire:model="form.status">
                                @foreach(\App\Enums\QuotationStatus::cases() as $statusEnum)
                                <option value="{{ $statusEnum->value }}">{{ $statusEnum->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <i class="ri-save-3-line align-middle me-1"></i> 
                                <span wire:loading.remove wire:target="save">حفظ التغييرات</span>
                                <span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm"></span> جاري الحفظ...</span>
                            </button>
                            <a href="{{ route('admin.quotations.index') }}" class="btn btn-soft-danger">إلغاء</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
