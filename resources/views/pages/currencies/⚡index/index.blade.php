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
    },
    rate: @entangle('form.exchange_rate'),
    equivalent: @entangle('form.equivalent'),
    updateEquivalent() {
        let val = parseFloat(this.rate);
        if (val > 0) {
            this.equivalent = (1 / val).toFixed(6);
        }
    },
    updateRate() {
        let val = parseFloat(this.equivalent);
        if (val > 0) {
            this.rate = (1 / val).toFixed(6);
        }
    }
}"
    x-on:open-currency-modal.window="showModal = true"
    x-on:close-currency-modal.window="showModal = false; $wire.cancel()"
    x-on:open-delete-modal.window="showDeleteModal = true"
    x-on:close-delete-modal.window="showDeleteModal = false"
    x-on:close-offcanvas.window="showOffcanvas = false">
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0">

                    <div class="row g-4 align-items-center">
                        <div class="col-sm-6">
                            <div class="search-box position-relative" x-data="{ search: @entangle('search') }">
                                <input type="text"
                                    class="form-control search bg-light border-light"
                                    placeholder="ابحث عن كود العملة، اسمها، أو رمزها..."
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
                                            @click="if(confirm('هل أنت متأكد من حذف السجلات المحددة؟')) $wire.deleteMultiple()">
                                            <i class="ri-delete-bin-2-line"></i> (<span x-text="selectedIds.length"></span>)
                                        </button>
                                        <button type="button" class="btn btn-soft-info">
                                            <i class="ri-printer-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-info" @click="showOffcanvas = true"><i class="ri-filter-3-line align-bottom me-1"></i> تصفية</button>
                                    <button type="button" class="btn btn-success add-btn" @click="$wire.cancel(); showModal = true" id="create-btn"><i class="ri-add-line align-bottom me-1"></i> إضافة عملة</button>
                                    <span class="dropdown">
                                        <button class="btn btn-soft-info btn-icon fs-14" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-settings-4-line"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item" href="#">Copy</a></li>
                                            <li><a class="dropdown-item" href="#">Move to pipline</a></li>
                                            <li><a class="dropdown-item" href="#">Add to exceptions</a></li>
                                            <li><a class="dropdown-item" href="#">Switch to common form view</a></li>
                                            <li><a class="dropdown-item" href="#">Reset form view to default</a></li>
                                        </ul>
                                    </span>
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
            <div class="card" id="currenciesList">
                <div class="card-header border-0">

                    <div class="row g-4 align-items-center">
                        <div class="col-sm-10">
                            <h5 class="card-title mb-0">إدارة بيانات العملات</h5>
                            <p><small>عرض وإدارة جميع بيانات العملات المسجلة في النظام.</small></p>
                        </div>

                        <div class="col-sm-auto ms-auto">
                            <div class="d-flex align-items-center gap-2" wire:ignore>
                                <span class="text-muted">عرض: </span>
                                <select class="form-control mb-0"
                                    wire:model.live="perPage">
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
                            <table class="table align-middle table-nowrap mb-0" id="currencyTable">
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
                                        <th @click="sortBy('code')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>كود العملة</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'code'">
                                                        <span class="text-muted opacity-50">↑↓</span>
                                                    </template>
                                                    <template x-if="sortField === 'code'">
                                                        <span>
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th @click="sortBy('name')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>اسم العملة</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'name'">
                                                        <span class="text-muted opacity-50">↑↓</span>
                                                    </template>
                                                    <template x-if="sortField === 'name'">
                                                        <span>
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th @click="sortBy('symbol')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>الرمز</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'symbol'">
                                                        <span class="text-muted opacity-50">↑↓</span>
                                                    </template>
                                                    <template x-if="sortField === 'symbol'">
                                                        <span>
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th @click="sortBy('exchange_rate')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>سعر الصرف</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'exchange_rate'">
                                                        <span class="text-muted opacity-50">↑↓</span>
                                                    </template>
                                                    <template x-if="sortField === 'exchange_rate'">
                                                        <span>
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th>الخصائص</th>

                                        <th @click="sortBy('status')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>الحالة</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'status'">
                                                        <span class="text-muted opacity-50">↑↓</span>
                                                    </template>
                                                    <template x-if="sortField === 'status'">
                                                        <span>
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>
                                        <th>انشئ في</th>

                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    @forelse($this->currenciesList as $currency)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chk_child"
                                                    value="{{ $currency->id }}"
                                                    x-model="selectedIds">
                                            </div>
                                        </th>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a class="edit-item-btn" href="javascript:void(0);" @click="$wire.editCurrency({{ $currency->id }}).then(() => showModal = true)"><i class="ri-pencil-fill align-bottom text-muted"></i></a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Delete">
                                                    <a class="remove-item-btn" href="javascript:void(0);" @click="$wire.confirmDelete({{ $currency->id }}).then(() => showDeleteModal = true)">
                                                        <i class="ri-delete-bin-fill align-bottom text-muted"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <div class="dropdown">
                                                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="ri-more-fill align-middle"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item view-item-btn" href="javascript:void(0);"><i class="ri-eye-fill align-bottom me-2 text-muted"></i>عرض</a></li>
                                                            <li><a class="dropdown-item edit-item-btn" href="#showModal" data-bs-toggle="modal"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> طباعة</a></li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="code"><a href="javascript:void(0);" class="fw-medium link-primary">{{ $currency->code }}</a></td>
                                        <td class="name">{{ $currency->name }}</td>
                                        <td class="symbol">{{ $currency->symbol }}</td>
                                        <td class="exchange_rate">{{ number_format($currency->exchange_rate, 4) }}</td>
                                        <td>
                                            @if($currency->is_local) <span class="badge bg-success-subtle text-success">محلية</span> @endif
                                            @if($currency->is_inventory) <span class="badge bg-info-subtle text-info">مخزون</span> @endif
                                        </td>
                                        <td class="status">
                                            <div class="form-check form-switch form-switch-md mb-2" dir="ltr">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    wire:click="toggleStatus({{ $currency->id }})"
                                                    {{ $currency->status->value === 1 ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="created_at">{{ optional($currency->created_at)->diffForHumans() }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9">
                                            <div class="noresult">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">عذراً! لم يتم العثور على نتائج</h5>
                                                    <p class="text-muted mb-0">لقد بحثنا في جميع العملات، ولم نعثر على أي نتائج مطابقة لبحثك.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{$this->currenciesList->links('livewire::custom-pagination-links')}}

                            <div wire:ignore.self
                                class="modal fade"
                                id="showModal"
                                tabindex="-1"
                                aria-labelledby="exampleModalLabel"
                                :class="{ 'show d-block': showModal }"
                                :aria-hidden="!showModal"
                                x-show="showModal"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-light p-3">
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                @if($form->currency)
                                                <i class="ri-edit-line me-2 text-warning"></i> تعديل بيانات العملة
                                                @else
                                                <i class="ri-money-dollar-circle-line me-2 text-success"></i> إضافة عملة جديدة
                                                @endif
                                            </h5>
                                            <button type="button" class="btn-close" aria-label="Close" @click="showModal = false; $wire.cancel()"></button>
                                        </div>
                                        <form wire:submit.prevent="submitCurrency" autocomplete="off">
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-2 pb-1 border-bottom">
                                                            <i class="ri-information-line me-1"></i> البيانات الأساسية
                                                        </h6>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-name" class="form-label">اسم العملة <span class="text-danger">*</span></label>
                                                        <input type="text" id="form-name" class="form-control @error('form.name') is-invalid @enderror"
                                                            wire:model.blur="form.name" placeholder="أدخل اسم العملة" />
                                                        @error('form.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-code" class="form-label">كود العملة <span class="text-danger">*</span></label>
                                                        <input type="text" id="form-code" class="form-control @error('form.code') is-invalid @enderror"
                                                            wire:model.blur="form.code" placeholder="مثال: USD, SAR..." />
                                                        @error('form.code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-symbol" class="form-label">رمز العملة</label>
                                                        <input type="text" id="form-symbol" class="form-control @error('form.symbol') is-invalid @enderror"
                                                            wire:model.blur="form.symbol" placeholder="مثال: $, ﷼..." />
                                                        @error('form.symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-fraction_name" class="form-label">اسم الكسر (أجزاء العملة)</label>
                                                        <div class="input-group">
                                                            <input type="text" id="form-fraction_name" class="form-control @error('form.fraction_name') is-invalid @enderror"
                                                                wire:model="form.fraction_name" placeholder="مثال: هللة، سنت..." />
                                                        </div>
                                                        @error('form.fraction_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-12 mt-2">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-2 pb-1 border-bottom">
                                                            <i class="ri-exchange-dollar-line me-1"></i> إعدادات الصرف والحالة
                                                        </h6>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-exchange_rate" class="form-label">سعر الصرف <span class="text-danger">*</span></label>
                                                        <input type="number" id="form-exchange_rate" class="form-control @error('form.exchange_rate') is-invalid @enderror"
                                                            x-model="rate"
                                                            @input="updateEquivalent()"
                                                            step="0.000001" min="0.000001" placeholder="سعر الصرف" />
                                                        @error('form.exchange_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-equivalent" class="form-label">المعادل</label>
                                                        <input type="number" id="form-equivalent" class="form-control @error('form.equivalent') is-invalid @enderror"
                                                            x-model="equivalent"
                                                            @input="updateRate()"
                                                            step="0.000001" min="0" placeholder="المعادل" />
                                                        @error('form.equivalent') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-max_exchange_rate" class="form-label">أعلى سعر صرف (Max)</label>
                                                        <input type="number" id="form-max_exchange_rate" class="form-control @error('form.max_exchange_rate') is-invalid @enderror"
                                                            wire:model="form.max_exchange_rate" step="0.000001" min="0" placeholder="أعلى سعر" />
                                                        @error('form.max_exchange_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-min_exchange_rate" class="form-label">أدنى سعر صرف (Min)</label>
                                                        <input type="number" id="form-min_exchange_rate" class="form-control @error('form.min_exchange_rate') is-invalid @enderror"
                                                            wire:model="form.min_exchange_rate" step="0.000001" min="0" placeholder="أدنى سعر" />
                                                        @error('form.min_exchange_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-12">
                                                        <label for="form-status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                                        <select id="form-status" class="form-select @error('form.status') is-invalid @enderror" wire:model.blur="form.status">
                                                            <option value="">اختر الحالة</option>
                                                            <option value="1">مفعل</option>
                                                            <option value="0">غير مفعل</option>
                                                        </select>
                                                        @error('form.status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-6 mt-4">
                                                        <div class="form-check form-switch form-switch-md" dir="ltr">
                                                            <label class="form-check-label" for="form-is_local">عملة محلية</label>
                                                            <input class="form-check-input @error('form.is_local') is-invalid @enderror" type="checkbox" id="form-is_local" wire:model="form.is_local" role="switch">
                                                            @error('form.is_local') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mt-4">
                                                        <div class="form-check form-switch form-switch-md" dir="ltr">
                                                            <label class="form-check-label" for="form-is_inventory">عملة المخزون الأساسية</label>
                                                            <input class="form-check-input @error('form.is_inventory') is-invalid @enderror" type="checkbox" id="form-is_inventory" wire:model="form.is_inventory" role="switch">
                                                            @error('form.is_inventory') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-12 mt-3">
                                                        <label for="form-notes" class="form-label">ملاحظات</label>
                                                        <textarea id="form-notes" class="form-control @error('form.notes') is-invalid @enderror"
                                                            wire:model="form.notes" rows="2" placeholder="أي ملاحظات إضافية..."></textarea>
                                                        @error('form.notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="button" class="btn btn-light" @click="showModal = false; $wire.cancel()">
                                                        <i class="ri-close-line me-1"></i> إلغاء
                                                    </button>
                                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                                        <span wire:loading.remove wire:target="submitCurrency,saveCurrency,updateCurrency">
                                                            @if($form->currency)
                                                            <i class="ri-save-line me-1"></i> تحديث البيانات
                                                            @else
                                                            <i class="ri-save-line me-1"></i> حفظ العملة
                                                            @endif
                                                        </span>
                                                        <span wire:loading wire:target="submitCurrency,saveCurrency,updateCurrency">
                                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> جاري الحفظ...
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--end modal-->

                            <!-- Delete Confirmation Modal -->
                            <div wire:ignore.self
                                id="deleteRecordModal"
                                class="modal fade zoomIn"
                                tabindex="-1"
                                aria-labelledby="deleteRecordLabel"
                                x-show="showDeleteModal"
                                :class="{ 'show d-block': showDeleteModal }"
                                :aria-hidden="!showDeleteModal">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn-close" aria-label="Close" @click="showDeleteModal = false"></button>
                                        </div>
                                        <div class="modal-body p-5 text-center">
                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                                            <div class="mt-4 text-center">
                                                <h4 class="fs-semibold">هل أنت متأكد من حذف هذه العملة؟</h4>
                                                <p class="text-muted fs-14 mb-4 pt-1">سيتم حذف العملة من قاعدة البيانات بشكل نهائي.</p>
                                                <div class="hstack gap-2 justify-content-center remove">
                                                    <button class="btn btn-light" @click="showDeleteModal = false">
                                                        <i class="ri-close-line me-1 align-middle"></i> إلغاء
                                                    </button>
                                                    <button class="btn btn-danger" wire:click="deleteCurrency" @click="showDeleteModal = false">
                                                        <i class="ri-delete-bin-fill me-1"></i> نعم، احذف!
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end delete modal-->


                            @include('partials.backend.currencies.offcanvas')

                        </div>
                    </div>

                </div>
                <!--end col-->
            </div>
        </div>

        {{-- Currency Form Modal Backdrop --}}
        <div class="modal-backdrop fade show" x-show="showModal" style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"></div>

        {{-- Delete Confirmation Modal Backdrop --}}
        <div class="modal-backdrop fade show" x-show="showDeleteModal" style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"></div>

        {{-- Offcanvas Backdrop --}}
        <div class="offcanvas-backdrop fade show" x-show="showOffcanvas" x-transition.opacity @click="showOffcanvas = false" style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"></div>
    </div>

    @push('scripts')
    <script>
        // SweetAlert2 Toast configuration
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
            // SweetAlert2 Toast notifications
            Livewire.on('notify', ({
                type,
                message
            }) => {
                const iconMap = {
                    success: 'success',
                    info: 'info',
                    warning: 'warning',
                    error: 'error',
                };
                Toast.fire({
                    icon: iconMap[type] ?? 'info',
                    title: message,
                });
            });

            // Close modal when Livewire dispatches close-modal
            Livewire.on('close-modal', () => {
                window.dispatchEvent(new CustomEvent('close-currency-modal'));
            });

            // Close offcanvas is now handled by Alpine `x-on:close-offcanvas.window`

        });
    </script>
    @endpush
</div>