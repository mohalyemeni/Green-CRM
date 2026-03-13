<div x-data="{
    selectedIds: @entangle('selectedIds'),
    sortField: @entangle('sortField'),
    sortDirection: @entangle('sortDirection'),
    showModal: false,
    showDeleteModal: false,
    showOffcanvas: false,
    sortBy(field) {
        $wire.sortBy(field);
    },
    toggleAll() {
        let checkboxes = document.querySelectorAll('input[name=chk_child]');
        if (this.selectedIds.length < checkboxes.length) {
            this.selectedIds = Array.from(checkboxes).map(el => el.value);
        } else {
            this.selectedIds = [];
        }
    }
}"
    x-on:open-modal.window="showModal = true"
    x-on:close-modal.window="showModal = false; $wire.cancel()"
    x-on:open-delete-modal.window="showDeleteModal = true"
    x-on:close-delete-modal.window="showDeleteModal = false"
    x-on:close-offcanvas.window="showOffcanvas = false">

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-6">
                            <div class="search-box position-relative" x-data="{ search: @entangle('search') }">
                                <input type="text"
                                    class="form-control search bg-light border-light"
                                    placeholder="ابحث عن عنوان الفرصة، الرقم المرجعي، الوصف..."
                                    wire:model.lazy="search">

                                <i class="ri-search-line search-icon" wire:loading.remove wire:target="search"></i>

                                <div class="spinner-border spinner-border-sm search-icon text-primary"
                                    role="status" wire:loading wire:target="search">
                                </div>

                                <button type="button"
                                    x-show="search.length > 0"
                                    x-on:click="search = ''; $wire.set('search', '')"
                                    class="btn btn-link position-absolute end-0 top-0 h-100 text-decoration-none text-muted"
                                    style="padding-right: 10px; z-index: 10;">
                                    <i class="ri-close-line fs-18"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-sm-auto ms-auto">
                            <div class="hstack gap-2">
                                <div x-show="selectedIds.length > 0" x-cloak x-transition>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-soft-danger add-btn"
                                            @click="if(confirm('هل أنت متأكد من حذف الفرص المحددة؟')) $wire.deleteMultiple()">
                                            <i class="ri-delete-bin-2-line"></i> (<span x-text="selectedIds.length"></span>)
                                        </button>
                                        <button type="button" class="btn btn-soft-info">
                                            <i class="ri-printer-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-info" @click="showOffcanvas = true">
                                        <i class="ri-filter-3-line align-bottom me-1"></i> تصفية
                                    </button>
                                    <button type="button" class="btn btn-success add-btn" @click="$wire.cancel(); showModal = true" id="create-btn">
                                        <i class="ri-add-line align-bottom me-1"></i> إضافة فرصة بيعية
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="opportunitiesList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-10">
                            <h5 class="card-title mb-0">سجل الفرص البيعية (Opportunities)</h5>
                            <p class="text-muted mb-0"><small>إدارة ومتابعة الصفقات والفرص البيعية وتطورها في قمع المبيعات.</small></p>
                        </div>
                        <div class="col-sm-auto ms-auto">
                            <div class="d-flex align-items-center gap-2" wire:ignore>
                                <span class="text-muted">عرض: </span>
                                <select class="form-control mb-0" wire:model.live="perPage">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <div class="table-responsive table-card mb-3">
                            <table class="table align-middle table-nowrap mb-0" id="opportunityTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll"
                                                    @change="toggleAll()"
                                                    :checked="selectedIds.length > 0 && selectedIds.length === document.querySelectorAll('input[name=chk_child]').length">
                                            </div>
                                        </th>
                                        <th>الإجراءات</th>
                                        <th @click="sortBy('opportunity_number')" style="cursor: pointer; user-select: none;">رقم الفرصة</th>
                                        <th @click="sortBy('title')" style="cursor: pointer; user-select: none;">عنوان الفرصة / العميل</th>
                                        <th @click="sortBy('expected_revenue')" style="cursor: pointer; user-select: none;">القيمة المتوقعة</th>
                                        <th>المرحلة (Stage)</th>
                                        <th @click="sortBy('expected_close_date')" style="cursor: pointer; user-select: none;">تاريخ الإغلاق المتوقع</th>
                                        <th @click="sortBy('priority')" style="cursor: pointer; user-select: none;">الأولوية</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    @forelse($this->opportunitiesList as $opp)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chk_child" value="{{ $opp->id }}" x-model="selectedIds">
                                            </div>
                                        </th>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item">
                                                    <a class="edit-item-btn" href="javascript:void(0);" @click="$wire.editOpportunity({{ $opp->id }}).then(() => showModal = true)"><i class="ri-pencil-fill align-bottom text-muted"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a class="remove-item-btn" href="javascript:void(0);" @click="$wire.confirmDelete({{ $opp->id }}).then(() => showDeleteModal = true)">
                                                        <i class="ri-delete-bin-fill align-bottom text-muted"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-body border">{{ $opp->opportunity_number }}</span>
                                        </td>
                                        <td>
                                            <h5 class="fs-14 my-1 fw-medium"><a href="#" class="text-body">{{ $opp->title }}</a></h5>
                                            <span class="text-muted fs-12"><i class="ri-user-line align-bottom me-1"></i> {{ $opp->customer?->name ?? 'غير محدد' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-primary">{{ number_format($opp->expected_revenue, 2) }}</span>
                                            <small class="text-muted">{{ $opp->currency?->code }}</small>
                                        </td>
                                        <td>
                                            @if($opp->stage)
                                            <span class="badge mb-1" style="background-color: {{ $opp->stage->color ?? '#405189' }}; color: #fff;">
                                                {{ $opp->stage->name }}
                                            </span>
                                            @else
                                            <span class="badge bg-light text-body border mb-1">غير محدد</span>
                                            @endif

                                            <div class="progress mt-1" style="height: 6px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $opp->probability }}%;" aria-valuenow="{{ $opp->probability }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <small class="text-muted fs-11">{{ $opp->probability }}% احتمالية</small>
                                        </td>
                                        <td>
                                            @if($opp->expected_close_date)
                                            {{ \Carbon\Carbon::parse($opp->expected_close_date)->format('Y-m-d') }}
                                            @else
                                            -
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                            $pColor = 'light'; $pText = 'غير محدد';
                                            switch($opp->priority) {
                                            case 'urgent': $pColor = 'danger'; $pText = 'عاجل جداً'; break;
                                            case 'high': $pColor = 'warning'; $pText = 'عالية'; break;
                                            case 'medium': $pColor = 'info'; $pText = 'متوسطة'; break;
                                            case 'low': $pColor = 'success'; $pText = 'منخفضة'; break;
                                            }
                                            @endphp
                                            <span class="badge bg-{{ $pColor }}-subtle text-{{ $pColor }}">{{ $pText }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="noresult">
                                                <div class="text-center p-5">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">عذراً! لم يتم العثور على نتائج</h5>
                                                    <p class="text-muted mb-0">لم نعثر على أي فرص بيعية مطابقة لبحثك.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{$this->opportunitiesList->links('livewire::custom-pagination-links')}}

                            <div wire:ignore.self class="modal fade" id="showModal" tabindex="-1" :class="{ 'show d-block': showModal }" :aria-hidden="!showModal" x-show="showModal" x-transition.opacity>
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content border-0">
                                        <div class="modal-header bg-light p-3">
                                            <h5 class="modal-title">
                                                @if($form->opportunity)
                                                <i class="ri-edit-line me-2 text-warning"></i> تعديل الفرصة البيعية
                                                @else
                                                <i class="ri-briefcase-add-line me-2 text-success"></i> إضافة فرصة بيعية جديدة
                                                @endif
                                            </h5>
                                            <button type="button" class="btn-close" aria-label="Close" @click="showModal = false; $wire.cancel()"></button>
                                        </div>
                                        <form wire:submit.prevent="submitOpportunity" autocomplete="off">
                                            <div class="modal-body p-4" style="max-height: 72vh; overflow-y: auto;">

                                                <h6 class="fs-14 text-primary fw-semibold text-uppercase mb-3"><i class="ri-information-line me-1"></i> البيانات الأساسية للفرصة</h6>
                                                <div class="row g-3 mb-4">
                                                    <div class="col-lg-3">
                                                        <label class="form-label">الرقم المرجعي</label>
                                                        <input type="text" class="form-control bg-light" wire:model="form.opportunity_number" placeholder="يولد تلقائياً" readonly />
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="form-label">عنوان الفرصة (اسم المشروع/الصفقة) <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('form.title') is-invalid @enderror" wire:model.blur="form.title" placeholder="مثال: تجهيز مكاتب الإدارة الجديدة" />
                                                        @error('form.title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="form-label">العميل المرتبط <span class="text-danger">*</span></label>
                                                        <select class="form-select @error('form.customer_id') is-invalid @enderror" wire:model.blur="form.customer_id">
                                                            <option value="">اختر العميل...</option>
                                                            @foreach(\App\Models\Customer::all() as $customer)
                                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('form.customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>

                                                <h6 class="fs-14 text-primary fw-semibold text-uppercase mb-3"><i class="ri-node-tree me-1"></i> مسار المبيعات والتبعية</h6>
                                                <div class="row g-3 mb-4">
                                                    <div class="col-lg-3">
                                                        <label class="form-label">الشركة <span class="text-danger">*</span></label>
                                                        <select class="form-select @error('form.company_id') is-invalid @enderror" wire:model.blur="form.company_id">
                                                            <option value="">اختر الشركة...</option>
                                                            @foreach(\App\Models\Company::where('status', 1)->get() as $company)
                                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('form.company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="form-label">مرحلة المبيعات (Stage)</label>
                                                        <select class="form-select" wire:model.blur="form.stage_id">
                                                            <option value="">اختر المرحلة...</option>
                                                            @foreach(\App\Models\PipelineStage::where('status', 1)->orderBy('sort_order')->get() as $stage)
                                                            <option value="{{ $stage->id }}">{{ $stage->name }} ({{ number_format($stage->probability, 0) }}%)</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="form-label">مصدر الفرصة</label>
                                                        <select class="form-select" wire:model.blur="form.opportunity_source_id">
                                                            <option value="">اختر المصدر...</option>
                                                            @foreach(\App\Models\OpportunitySource::where('status', 1)->get() as $source)
                                                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="form-label">الموظف المسؤول (Assigned To)</label>
                                                        <select class="form-select" wire:model.blur="form.assigned_to">
                                                            <option value="">تعيين إلى...</option>
                                                            @foreach(\App\Models\User::all() as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <h6 class="fs-14 text-primary fw-semibold text-uppercase mb-3"><i class="ri-money-dollar-circle-line me-1"></i> المالية والتوقيت</h6>
                                                <div class="row g-3 mb-4">
                                                    <div class="col-lg-4">
                                                        <label class="form-label">الإيراد المتوقع (Expected Revenue)</label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control" wire:model.blur="form.expected_revenue" placeholder="0.00" />
                                                            <select class="form-select" style="max-width: 120px;" wire:model.blur="form.currency_id">
                                                                <option value="">العملة...</option>
                                                                @foreach(\App\Models\Currency::where('status', 1)->get() as $currency)
                                                                <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <label class="form-label">الاحتمالية (%)</label>
                                                        <input type="number" class="form-control" wire:model.blur="form.probability" min="0" max="100" placeholder="0 - 100" />
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="form-label">تاريخ الإغلاق المتوقع</label>
                                                        <input type="date" class="form-control" wire:model.blur="form.expected_close_date" />
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="form-label">الأولوية <span class="text-danger">*</span></label>
                                                        <select class="form-select" wire:model.blur="form.priority">
                                                            <option value="urgent">عاجل جداً (Urgent)</option>
                                                            <option value="high">عالية (High)</option>
                                                            <option value="medium">متوسطة (Medium)</option>
                                                            <option value="low">منخفضة (Low)</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <h6 class="fs-14 text-primary fw-semibold text-uppercase mb-3"><i class="ri-file-text-line me-1"></i> التفاصيل والخسارة</h6>
                                                <div class="row g-3">
                                                    <div class="col-lg-12">
                                                        <label class="form-label">وصف الفرصة (Description)</label>
                                                        <textarea class="form-control" wire:model="form.description" rows="2" placeholder="أدخل تفاصيل ما يحتاجه العميل بالضبط..."></textarea>
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <label class="form-label text-danger">سبب الخسارة (في حال فشل الصفقة)</label>
                                                        <select class="form-select border-danger" wire:model.blur="form.lost_reason_id">
                                                            <option value="">بدون (الفرصة مستمرة)</option>
                                                            @foreach(\App\Models\LostReason::where('status', 1)->get() as $reason)
                                                            <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <label class="form-label text-danger">ملاحظات الخسارة</label>
                                                        <input type="text" class="form-control border-danger" wire:model.blur="form.lost_reason_notes" placeholder="لماذا خسرنا هذه الصفقة؟" />
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="modal-footer bg-light p-3">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="button" class="btn btn-light" @click="showModal = false; $wire.cancel()">إلغاء</button>
                                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                                        <span wire:loading.remove wire:target="submitOpportunity"><i class="ri-save-line me-1"></i> حفظ بيانات الفرصة</span>
                                                        <span wire:loading wire:target="submitOpportunity"><span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div wire:ignore.self id="deleteRecordModal" class="modal fade zoomIn" tabindex="-1" x-show="showDeleteModal" :class="{ 'show d-block': showDeleteModal }">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-0"><button type="button" class="btn-close" @click="showDeleteModal = false"></button></div>
                                        <div class="modal-body p-5 text-center">
                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                                            <div class="mt-4 text-center">
                                                <h4 class="fs-semibold">حذف الفرصة البيعية؟</h4>
                                                <p class="text-muted">هل أنت متأكد؟ سيتم نقل هذه الفرصة إلى سلة المهملات.</p>
                                                <div class="hstack gap-2 justify-content-center remove mt-4">
                                                    <button class="btn btn-light" @click="showDeleteModal = false">إلغاء</button>
                                                    <button class="btn btn-danger" wire:click="deleteOpportunity" @click="showDeleteModal = false">نعم، احذف!</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('partials.backend.opportunities.offcanvas')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-backdrop fade show" x-show="showModal || showDeleteModal" style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"></div>
        <div class="offcanvas-backdrop fade show" x-show="showOffcanvas" x-transition.opacity @click="showOffcanvas = false" style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"></div>
    </div>

    @push('scripts')
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', ({
                type,
                message
            }) => {
                const iconMap = {
                    success: 'success',
                    info: 'info',
                    warning: 'warning',
                    error: 'error'
                };
                Toast.fire({
                    icon: iconMap[type] ?? 'info',
                    title: message
                });
            });

            Livewire.on('close-modal', () => {
                window.dispatchEvent(new CustomEvent('close-modal'));
            });
        });
    </script>
    @endpush
</div>