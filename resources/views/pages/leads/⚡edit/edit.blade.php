<div>
    {{-- شريط العنوان --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">تعديل: {{ $lead->first_name }} {{ $lead->last_name }}</h4>
                <div class="page-title-right hstack gap-2">
                    <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn btn-soft-info">
                        <i class="ri-eye-line align-bottom me-1"></i> عرض الملف
                    </a>
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-soft-secondary">
                        <i class="ri-arrow-right-line align-bottom me-1"></i> القائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="save" autocomplete="off">
        <div class="row g-4">

            {{-- قسم 1: البيانات الشخصية --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="bg-primary-subtle text-primary p-2 rounded-circle me-2">
                                <i class="ri-user-line fs-18"></i>
                            </span>
                            <h6 class="text-uppercase fw-bold mb-0">البيانات الشخصية والتواصل</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-medium">الاسم الأول <span class="text-danger">*</span></label>
                                <input type="text" id="first_name"
                                    class="form-control @error('form.first_name') is-invalid @enderror"
                                    wire:model.blur="form.first_name">
                                @error('form.first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label fw-medium">الاسم الأخير</label>
                                <input type="text" id="last_name"
                                    class="form-control"
                                    wire:model.blur="form.last_name">
                            </div>
                            <div class="col-md-6">
                                <label for="mobile" class="form-label fw-medium">رقم الجوال <span class="text-danger">*</span></label>
                                <input type="text" id="mobile" dir="ltr"
                                    class="form-control @error('form.mobile') is-invalid @enderror"
                                    wire:model.blur="form.mobile">
                                @error('form.mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-medium">رقم الهاتف</label>
                                <input type="text" id="phone" dir="ltr"
                                    class="form-control"
                                    wire:model.blur="form.phone">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-medium">البريد الإلكتروني</label>
                                <input type="email" id="email" dir="ltr"
                                    class="form-control @error('form.email') is-invalid @enderror"
                                    wire:model.blur="form.email">
                                @error('form.email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="company_name" class="form-label fw-medium">اسم الشركة</label>
                                <input type="text" id="company_name"
                                    class="form-control"
                                    wire:model.blur="form.company_name">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- قسم 2: تفاصيل الصفقة --}}
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="bg-success-subtle text-success p-2 rounded-circle me-2">
                                <i class="ri-settings-4-line fs-18"></i>
                            </span>
                            <h6 class="text-uppercase fw-bold mb-0">تفاصيل الصفقة</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="lead_source_id" class="form-label fw-medium">المصدر <span class="text-danger">*</span></label>
                                <select id="lead_source_id"
                                    class="form-select @error('form.lead_source_id') is-invalid @enderror"
                                    wire:model="form.lead_source_id">
                                    <option value="">-- اختر المصدر --</option>
                                    @foreach($this->sources as $source)
                                        <option value="{{ $source->id }}">{{ $source->name }}</option>
                                    @endforeach
                                </select>
                                @error('form.lead_source_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="lead_status_id" class="form-label fw-medium">الحالة</label>
                                <select id="lead_status_id" class="form-select" wire:model="form.lead_status_id">
                                    <option value="">-- اختر الحالة --</option>
                                    @foreach($this->statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="assigned_to" class="form-label fw-medium">الموظف المسؤول</label>
                                <select id="assigned_to" class="form-select" wire:model="form.assigned_to">
                                    <option value="">-- بدون مسؤول --</option>
                                    @foreach($this->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
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
                                            name="priority_radio"
                                            id="priority_{{ $p->value }}"
                                            value="{{ $p->value }}">
                                        <label class="form-check-label badge bg-{{ $p->color() }}-subtle text-{{ $p->color() }} border border-{{ $p->color() }}" for="priority_{{ $p->value }}">
                                            {{ $p->label() }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
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
                            <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn btn-ghost-danger">
                                <i class="ri-close-line align-bottom me-1"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ri-refresh-line align-bottom me-1"></i> حفظ التعديلات
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
