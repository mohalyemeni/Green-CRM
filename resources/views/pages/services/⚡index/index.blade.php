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
    x-on:open-service-modal.window="showModal = true"
    x-on:close-service-modal.window="showModal = false; $wire.cancel()"
    x-on:open-delete-modal.window="showDeleteModal = true"
    x-on:close-delete-modal.window="showDeleteModal = false"
    x-on:close-offcanvas.window="showOffcanvas = false">

    {{-- شريط البحث والأزرار --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-6">
                            <div class="search-box position-relative" x-data="{ search: @entangle('search') }">
                                <input type="text"
                                    class="form-control search bg-light border-light"
                                    placeholder="ابحث عن اسم الخدمة، الكود، الوصف..."
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
                                            @click="if(confirm('هل أنت متأكد من حذف الخدمات المحددة؟')) $wire.deleteMultiple()">
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
                                    <button type="button" class="btn btn-success add-btn"
                                        @click="$wire.cancel(); showModal = true" id="create-btn">
                                        <i class="ri-add-line align-bottom me-1"></i> إضافة خدمة
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول البيانات --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="servicesList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-10">
                            <h5 class="card-title mb-0">إدارة الخدمات</h5>
                            <p><small>عرض وإدارة جميع الخدمات المسجلة في النظام.</small></p>
                        </div>
                        <div class="col-sm-auto ms-auto">
                            <div class="d-flex align-items-center gap-2" wire:ignore>
                                <span class="text-muted">عرض: </span>
                                <select class="form-control mb-0" wire:model.live="perPage">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="500">500</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div>
                        <div class="table-responsive table-card mb-3">
                            <table class="table align-middle table-nowrap mb-0" id="servicesTable">
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

                                        <th @click="sortBy('name')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>اسم الخدمة</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'name'"><span class="text-muted opacity-50">↑↓</span></template>
                                                    <template x-if="sortField === 'name'">
                                                        <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th @click="sortBy('code')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>الكود</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'code'"><span class="text-muted opacity-50">↑↓</span></template>
                                                    <template x-if="sortField === 'code'">
                                                        <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th>المجموعة</th>

                                        <th @click="sortBy('price')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>سعر البيع</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'price'"><span class="text-muted opacity-50">↑↓</span></template>
                                                    <template x-if="sortField === 'price'">
                                                        <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th>الضريبة</th>

                                        <th @click="sortBy('status')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>الحالة</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'status'"><span class="text-muted opacity-50">↑↓</span></template>
                                                    <template x-if="sortField === 'status'">
                                                        <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th @click="sortBy('created_at')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>أنشئ في</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'created_at'"><span class="text-muted opacity-50">↑↓</span></template>
                                                    <template x-if="sortField === 'created_at'">
                                                        <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    @forelse($this->servicesList as $service)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chk_child"
                                                    value="{{ $service->id }}"
                                                    x-model="selectedIds">
                                            </div>
                                        </th>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="تعديل">
                                                    <a class="edit-item-btn" href="javascript:void(0);"
                                                        @click="$wire.editService({{ $service->id }}).then(() => showModal = true)">
                                                        <i class="ri-pencil-fill align-bottom text-muted"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="حذف">
                                                    <a class="remove-item-btn" href="javascript:void(0);"
                                                        @click="$wire.confirmDelete({{ $service->id }}).then(() => showDeleteModal = true)">
                                                        <i class="ri-delete-bin-fill align-bottom text-muted"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>

                                        <td class="name">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs rounded-circle bg-success-subtle text-success d-flex justify-content-center align-items-center me-2">
                                                    <i class="ri-service-line fs-14"></i>
                                                </div>
                                                <div>
                                                    <h5 class="fs-14 my-1 fw-medium">{{ $service->name }}</h5>
                                                    @if($service->serviceGroup)
                                                    <span class="text-muted mb-0 fs-12">
                                                        <i class="ri-stack-line me-1 align-bottom"></i>
                                                        {{ $service->serviceGroup->name }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            @if($service->code)
                                            <code class="bg-light px-2 py-1 rounded fs-12">{{ $service->code }}</code>
                                            @else
                                            <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($service->serviceGroup)
                                            <span class="badge bg-primary-subtle text-primary">{{ $service->serviceGroup->name }}</span>
                                            @else
                                            <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        <td class="price">
                                            <div class="fw-semibold text-dark">{{ number_format((float) $service->price, 2) }}</div>
                                            @if((float)$service->base_cost > 0)
                                            <small class="text-muted">التكلفة: {{ number_format((float) $service->base_cost, 2) }}</small>
                                            @endif
                                        </td>

                                        <td>
                                            @if($service->is_taxable)
                                            <span class="badge bg-warning-subtle text-warning">
                                                <i class="ri-percent-line me-1"></i>{{ (float) $service->tax_rate }}%
                                            </span>
                                            @else
                                            <span class="badge bg-secondary-subtle text-secondary">معفاة</span>
                                            @endif
                                        </td>

                                        <td class="status">
                                            <div class="form-check form-switch form-switch-md mb-2" dir="ltr">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    wire:click="toggleStatus({{ $service->id }})"
                                                    {{ $service->status->value === 1 ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="created_at">{{ optional($service->created_at)->diffForHumans() }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9">
                                            <div class="noresult">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                                        colors="primary:#121331,secondary:#08a88a"
                                                        style="width:75px;height:75px">
                                                    </lord-icon>
                                                    <h5 class="mt-2">عذراً! لم يتم العثور على نتائج</h5>
                                                    <p class="text-muted mb-0">لم نعثر على أي خدمة مطابقة لبحثك.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- Pagination --}}
                            {{ $this->servicesList->links('livewire::custom-pagination-links') }}

                            {{-- ================================================================ --}}
                            {{-- Modal: إضافة / تعديل الخدمة                                    --}}
                            {{-- ================================================================ --}}
                            <div wire:ignore.self
                                class="modal fade" id="showServiceModal" tabindex="-1" aria-labelledby="serviceModalLabel"
                                :class="{ 'show d-block': showModal }" :aria-hidden="!showModal"
                                x-show="showModal" x-transition.opacity>
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-light p-3">
                                            <h5 class="modal-title" id="serviceModalLabel">
                                                @if($form->service)
                                                <i class="ri-edit-line me-2 text-warning"></i> تعديل بيانات الخدمة
                                                @else
                                                <i class="ri-add-line me-2 text-success"></i> إضافة خدمة جديدة
                                                @endif
                                            </h5>
                                            <button type="button" class="btn-close" aria-label="Close"
                                                @click="showModal = false; $wire.cancel()"></button>
                                        </div>

                                        <form wire:submit.prevent="submitService" autocomplete="off">
                                            <div class="modal-body p-4" style="max-height: 80vh; overflow-y: auto;">
                                                <div class="row g-4">

                                                    {{-- =============================== --}}
                                                    {{-- قسم 1: المعلومات الأساسية    --}}
                                                    {{-- =============================== --}}
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="bg-primary-subtle text-primary p-2 rounded-circle me-2">
                                                                <i class="ri-information-line fs-18"></i>
                                                            </span>
                                                            <h6 class="text-uppercase fw-bold mb-0">المعلومات الأساسية</h6>
                                                        </div>
                                                        <hr class="mt-0">
                                                    </div>

                                                    {{-- اسم الخدمة --}}
                                                    <div class="col-md-6">
                                                        <label for="form-name" class="form-label fw-medium">اسم الخدمة <span class="text-danger">*</span></label>
                                                        <input type="text" id="form-name"
                                                            class="form-control @error('form.name') is-invalid @enderror"
                                                            wire:model.blur="form.name"
                                                            placeholder="مثال: تأشيرة سياحية، خدمة استقدام...">
                                                        @error('form.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- كود الخدمة --}}
                                                    <div class="col-md-3">
                                                        <label for="form-code" class="form-label fw-medium">كود الخدمة</label>
                                                        <input type="text" id="form-code"
                                                            class="form-control @error('form.code') is-invalid @enderror"
                                                            wire:model.blur="form.code"
                                                            placeholder="مثال: SRV-001">
                                                        @error('form.code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- مجموعة الخدمة --}}
                                                    <div class="col-md-3">
                                                        <label for="form-service_group_id" class="form-label fw-medium">مجموعة الخدمة <span class="text-danger">*</span></label>
                                                        <select id="form-service_group_id"
                                                            class="form-select @error('form.service_group_id') is-invalid @enderror"
                                                            wire:model="form.service_group_id">
                                                            <option value="">-- اختر المجموعة --</option>
                                                            @foreach($this->activeServiceGroups as $group)
                                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('form.service_group_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- الوصف --}}
                                                    <div class="col-md-6">
                                                        <label for="form-description" class="form-label fw-medium">تعريف بالخدمة (الوصف)</label>
                                                        <textarea id="form-description"
                                                            class="form-control @error('form.description') is-invalid @enderror"
                                                            rows="3" wire:model="form.description"
                                                            placeholder="اكتب وصفاً موجزاً للخدمة وطبيعتها..."></textarea>
                                                        @error('form.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- المتطلبات --}}
                                                    <div class="col-md-6">
                                                        <label for="form-requirements" class="form-label fw-medium">المتطلبات اللازمة</label>
                                                        <textarea id="form-requirements"
                                                            class="form-control @error('form.requirements') is-invalid @enderror"
                                                            rows="3" wire:model="form.requirements"
                                                            placeholder="اذكر الأوراق أو الشروط المطلوبة لإتمام هذه الخدمة..."></textarea>
                                                        @error('form.requirements') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- =============================== --}}
                                                    {{-- قسم 2: البيانات المالية       --}}
                                                    {{-- =============================== --}}
                                                    <div class="col-12 mt-2">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="bg-success-subtle text-success p-2 rounded-circle me-2">
                                                                <i class="ri-money-dollar-circle-line fs-18"></i>
                                                            </span>
                                                            <h6 class="text-uppercase fw-bold mb-0">البيانات المالية</h6>
                                                        </div>
                                                        <hr class="mt-0">
                                                    </div>

                                                    {{-- تكلفة الخدمة --}}
                                                    <div class="col-md-3">
                                                        <label for="form-base_cost" class="form-label fw-medium">تكلفة الخدمة (base_cost)</label>
                                                        <div class="input-group">
                                                            <input type="number" id="form-base_cost" step="0.01" min="0"
                                                                class="form-control @error('form.base_cost') is-invalid @enderror"
                                                                wire:model="form.base_cost"
                                                                placeholder="0.00">
                                                            <span class="input-group-text"><i class="ri-money-dollar-box-line"></i></span>
                                                            @error('form.base_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>

                                                    {{-- سعر البيع --}}
                                                    <div class="col-md-3">
                                                        <label for="form-price" class="form-label fw-medium">سعر البيع <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="number" id="form-price" step="0.01" min="0"
                                                                class="form-control @error('form.price') is-invalid @enderror"
                                                                wire:model="form.price"
                                                                placeholder="0.00">
                                                            <span class="input-group-text"><i class="ri-price-tag-3-line"></i></span>
                                                            @error('form.price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>

                                                    {{-- أقل سعر مسموح --}}
                                                    <div class="col-md-3">
                                                        <label for="form-min_price" class="form-label fw-medium">السعر الأدنى المسموح</label>
                                                        <div class="input-group">
                                                            <input type="number" id="form-min_price" step="0.01" min="0"
                                                                class="form-control @error('form.min_price') is-invalid @enderror"
                                                                wire:model="form.min_price"
                                                                placeholder="0.00">
                                                            <span class="input-group-text"><i class="ri-arrow-down-circle-line"></i></span>
                                                            @error('form.min_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>

                                                    {{-- أقصى خصم --}}
                                                    <div class="col-md-3">
                                                        <label for="form-max_discount" class="form-label fw-medium">أقصى خصم مسموح</label>
                                                        <div class="input-group">
                                                            <input type="number" id="form-max_discount" step="0.01" min="0"
                                                                class="form-control @error('form.max_discount') is-invalid @enderror"
                                                                wire:model="form.max_discount"
                                                                placeholder="0.00">
                                                            <span class="input-group-text" x-text="$wire.get('form.discount_type') === 'percentage' ? '%' : '$'"></span>
                                                            @error('form.max_discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>

                                                    {{-- نوع الخصم --}}
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium">نوع الخصم</label>
                                                        <div class="d-flex gap-3 mt-1">
                                                            @foreach(\App\Enums\DiscountType::cases() as $dtype)
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    name="discount_type_radio"
                                                                    id="dtype_{{ $dtype->value }}"
                                                                    wire:model="form.discount_type"
                                                                    value="{{ $dtype->value }}">
                                                                <label class="form-check-label text-{{ $dtype->color() }}" for="dtype_{{ $dtype->value }}">
                                                                    <i class="me-1">{{ $dtype->symbol() }}</i> {{ $dtype->label() }}
                                                                </label>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        @error('form.discount_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- =============================== --}}
                                                    {{-- قسم 3: بيانات الضريبة والنظام --}}
                                                    {{-- =============================== --}}
                                                    <div class="col-12 mt-2">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="bg-warning-subtle text-warning p-2 rounded-circle me-2">
                                                                <i class="ri-percent-line fs-18"></i>
                                                            </span>
                                                            <h6 class="text-uppercase fw-bold mb-0">الضريبة وحالة الخدمة</h6>
                                                        </div>
                                                        <hr class="mt-0">
                                                    </div>

                                                    {{-- خاضع للضريبة --}}
                                                    <div class="col-md-4" x-data="{ isTaxable: @entangle('form.is_taxable') }">
                                                        <div class="form-check form-switch form-switch-lg shadow-sm p-3 border rounded h-100 d-flex align-items-center gap-3">
                                                            <input class="form-check-input ms-0" type="checkbox"
                                                                wire:model="form.is_taxable"
                                                                x-model="isTaxable"
                                                                id="isTaxableCheck" role="switch">
                                                            <label class="form-check-label fw-bold text-dark" for="isTaxableCheck">
                                                                <i class="ri-tax-2-line me-1 text-warning"></i>
                                                                خاضعة للضريبة
                                                            </label>
                                                        </div>
                                                    </div>

                                                    {{-- نسبة الضريبة --}}
                                                    <div class="col-md-4" x-data="{ isTaxable: @entangle('form.is_taxable') }">
                                                        <label for="form-tax_rate" class="form-label fw-medium">نسبة الضريبة (%)</label>
                                                        <div class="input-group">
                                                            <input type="number" id="form-tax_rate" step="0.01" min="0" max="100"
                                                                class="form-control @error('form.tax_rate') is-invalid @enderror"
                                                                wire:model="form.tax_rate"
                                                                placeholder="مثال: 15"
                                                                :disabled="!isTaxable">
                                                            <span class="input-group-text">%</span>
                                                            @error('form.tax_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>

                                                    {{-- حالة الخدمة --}}
                                                    <div class="col-md-4">
                                                        <div class="form-check form-switch form-switch-lg shadow-sm p-3 border rounded h-100 d-flex align-items-center gap-3">
                                                            <input class="form-check-input ms-0" type="checkbox"
                                                                wire:model.number="form.status" id="statusCheck"
                                                                true-value="{{ \App\Enums\ActiveStatus::ACTIVE->value }}"
                                                                false-value="{{ \App\Enums\ActiveStatus::INACTIVE->value }}"
                                                                checked
                                                                role="switch">
                                                            <label class="form-check-label fw-bold text-dark" for="statusCheck">
                                                                <i class="ri-toggle-line me-1 text-success"></i>
                                                                تفعيل الخدمة
                                                            </label>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="modal-footer bg-light border-top-0">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="button" class="btn btn-ghost-danger"
                                                        @click="showModal = false; $wire.cancel()">
                                                        <i class="ri-close-line align-bottom me-1"></i> إغلاق
                                                    </button>
                                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                                        <span wire:loading.remove wire:target="submitService">
                                                            @if($form->service)
                                                            <i class="ri-refresh-line align-bottom me-1"></i> تحديث البيانات
                                                            @else
                                                            <i class="ri-save-3-line align-bottom me-1"></i> حفظ الخدمة
                                                            @endif
                                                        </span>
                                                        <span wire:loading wire:target="submitService">
                                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> جاري الحفظ...
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- ======================================== --}}
                            {{-- Modal: تأكيد الحذف                      --}}
                            {{-- ======================================== --}}
                            <div wire:ignore.self
                                id="deleteServiceModal" class="modal fade zoomIn" tabindex="-1"
                                x-show="showDeleteModal"
                                :class="{ 'show d-block': showDeleteModal }" :aria-hidden="!showDeleteModal">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn-close" aria-label="Close"
                                                @click="showDeleteModal = false">
                                            </button>
                                        </div>
                                        <div class="modal-body p-5 text-center">
                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                                colors="primary:#405189,secondary:#f06548"
                                                style="width:90px;height:90px">
                                            </lord-icon>
                                            <div class="mt-4 text-center">
                                                <h4 class="fs-semibold">هل أنت متأكد من حذف هذه الخدمة؟</h4>
                                                <p class="text-muted fs-14 mb-4 pt-1">سيتم نقل الخدمة لسلة المهملات.</p>
                                                <div class="hstack gap-2 justify-content-center remove">
                                                    <button class="btn btn-light" @click="showDeleteModal = false">
                                                        <i class="ri-close-line me-1 align-middle"></i> إلغاء
                                                    </button>
                                                    <button class="btn btn-danger"
                                                        wire:click="deleteService"
                                                        @click="showDeleteModal = false">
                                                        <i class="ri-delete-bin-fill me-1"></i> نعم، احذف!
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Offcanvas التصفية --}}
                            @include('partials.backend.services.offcanvas')

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Backdrops --}}
        <div class="modal-backdrop fade show" x-show="showModal" style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"></div>
        <div class="modal-backdrop fade show" x-show="showDeleteModal" style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"></div>
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
                window.dispatchEvent(new CustomEvent('close-service-modal'));
            });
        });
    </script>
    @endpush

</div>