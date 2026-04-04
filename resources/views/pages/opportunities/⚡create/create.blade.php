<div>
    {{-- شريط العنوان --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">إضافة فرصة بيعية جديدة</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.opportunities.index') }}" class="btn btn-soft-secondary">
                        <i class="ri-arrow-right-line align-bottom me-1"></i> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="save" autocomplete="off">
        <div class="row g-4">

            {{-- ============================= --}}
            {{-- قسم 1: البيانات الأساسية      --}}
            {{-- ============================= --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="bg-success-subtle text-success p-2 rounded-circle me-2">
                                <i class="ri-funds-line fs-18"></i>
                            </span>
                            <h6 class="text-uppercase fw-bold mb-0">بيانات الفرصة البيعية</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="opp_title" class="form-label fw-medium">عنوان الفرصة <span class="text-danger">*</span></label>
                                <input type="text" id="opp_title"
                                    class="form-control @error('form.title') is-invalid @enderror"
                                    wire:model.blur="form.title"
                                    placeholder="مثال: عرض توريد معدات لشركة الأمل">
                                @error('form.title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="opp_customer" class="form-label fw-medium">العميل <span class="text-danger">*</span></label>
                                <select id="opp_customer"
                                    class="form-select @error('form.customer_id') is-invalid @enderror"
                                    wire:model="form.customer_id">
                                    <option value="">-- اختر العميل --</option>
                                    @foreach($this->customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} — {{ $customer->mobile }}</option>
                                    @endforeach
                                </select>
                                @error('form.customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="opp_source" class="form-label fw-medium">المصدر</label>
                                <select id="opp_source" class="form-select" wire:model="form.opportunity_source_id">
                                    <option value="">-- اختر المصدر --</option>
                                    @foreach($this->sources as $source)
                                        <option value="{{ $source->id }}">{{ $source->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="opp_stage" class="form-label fw-medium">مرحلة المبيعات</label>
                                <select id="opp_stage" class="form-select" wire:model="form.stage_id">
                                    <option value="">-- اختر المرحلة --</option>
                                    @foreach($this->stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->name }} ({{ $stage->probability }}%)</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="opp_company" class="form-label fw-medium">الشركة <span class="text-danger">*</span></label>
                                <select id="opp_company"
                                    class="form-select @error('form.company_id') is-invalid @enderror"
                                    wire:model="form.company_id">
                                    <option value="">-- اختر الشركة --</option>
                                    @foreach($this->companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('form.company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="opp_revenue" class="form-label fw-medium">الإيراد المتوقع</label>
                                <div class="input-group">
                                    <input type="number" id="opp_revenue" dir="ltr"
                                        class="form-control @error('form.expected_revenue') is-invalid @enderror"
                                        wire:model.blur="form.expected_revenue"
                                        placeholder="0.00" step="0.01" min="0">
                                    <select class="input-group-text form-select" style="max-width: 100px;" wire:model="form.currency_id">
                                        <option value="">عملة</option>
                                        @foreach($this->currencies as $currency)
                                            <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('form.expected_revenue') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="opp_probability" class="form-label fw-medium">نسبة الاحتمالية (%)</label>
                                <div class="input-group">
                                    <input type="number" id="opp_probability" dir="ltr"
                                        class="form-control"
                                        wire:model.blur="form.probability"
                                        placeholder="0" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="opp_close_date" class="form-label fw-medium">تاريخ الإغلاق المتوقع</label>
                                <input type="date" id="opp_close_date"
                                    class="form-control"
                                    wire:model="form.expected_close_date">
                            </div>

                            <div class="col-md-6">
                                <label for="opp_type" class="form-label fw-medium">نوع الفرصة</label>
                                <input type="text" id="opp_type"
                                    class="form-control"
                                    wire:model.blur="form.opportunity_type"
                                    placeholder="مثال: خدمات، منتجات، استشارات...">
                            </div>

                            <div class="col-12">
                                <label for="opp_description" class="form-label fw-medium">الوصف / ملاحظات</label>
                                <textarea id="opp_description"
                                    class="form-control"
                                    rows="3"
                                    wire:model="form.description"
                                    placeholder="أدخل تفاصيل الفرصة البيعية..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================= --}}
            {{-- قسم 2: الإعدادات           --}}
            {{-- ============================= --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="bg-primary-subtle text-primary p-2 rounded-circle me-2">
                                <i class="ri-settings-4-line fs-18"></i>
                            </span>
                            <h6 class="text-uppercase fw-bold mb-0">إعدادات الفرصة</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="opp_assigned_to" class="form-label fw-medium">الموظف المسؤول</label>
                                <select id="opp_assigned_to" class="form-select" wire:model="form.assigned_to">
                                    <option value="">-- بدون مسؤول --</option>
                                    @foreach($this->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">الأولوية</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    @foreach($this->priorities as $p)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                            wire:model="form.priority"
                                            name="opp_priority_radio"
                                            id="opp_p_{{ $p['value'] }}"
                                            value="{{ $p['value'] }}">
                                        <label class="form-check-label badge bg-{{ $p['color'] }}-subtle text-{{ $p['color'] }} border border-{{ $p['color'] }}" for="opp_p_{{ $p['value'] }}">
                                            {{ $p['label'] }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="opp_lost_reason" class="form-label fw-medium">سبب الخسارة</label>
                                <select id="opp_lost_reason" class="form-select @error('form.lost_reason_id') is-invalid @enderror" wire:model="form.lost_reason_id">
                                    <option value="">-- لا يوجد --</option>
                                    @foreach($this->lostReasons as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                    @endforeach
                                </select>
                                @error('form.lost_reason_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="opp_lost_notes" class="form-label fw-medium">ملاحظات الخسارة</label>
                                <textarea id="opp_lost_notes"
                                    class="form-control @error('form.lost_reason_notes') is-invalid @enderror"
                                    rows="2"
                                    wire:model="form.lost_reason_notes"
                                    placeholder="تفاصيل إضافية عن سبب الخسارة..."></textarea>
                                @error('form.lost_reason_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- أزرار الحفظ --}}
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="hstack gap-2 justify-content-end">
                            <a href="{{ route('admin.opportunities.index') }}" class="btn btn-ghost-danger">
                                <i class="ri-close-line align-bottom me-1"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ri-save-3-line align-bottom me-1"></i> حفظ الفرصة البيعية
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span> جاري الحفظ...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', ({ type, message }) => {
                Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true })
                    .fire({ icon: type, title: message });
            });
        });
    </script>
    @endpush
</div>
