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
    x-on:open-branch-modal.window="showModal = true"
    x-on:close-branch-modal.window="showModal = false; $wire.cancel()"
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
                                    placeholder="ابحث عن كود الفرع، اسم الفرع، السجل التجاري..."
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
                                    <button type="button" class="btn btn-success add-btn" @click="$wire.cancel(); showModal = true" id="create-btn"><i class="ri-add-line align-bottom me-1"></i> إضافة فرع</button>
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
            <div class="card" id="branchesList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-10">
                            <h5 class="card-title mb-0">إدارة الفروع</h5>
                            <p><small>عرض وإدارة جميع فروع الشركات المسجلة في النظام.</small></p>
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
                            <table class="table align-middle table-nowrap mb-0" id="branchTable">
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
                                                <span>كود الفرع</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'code'"><span class="text-muted opacity-50">↑↓</span></template>
                                                    <template x-if="sortField === 'code'">
                                                        <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th @click="sortBy('name')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>الفرع والشركة</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'name'"><span class="text-muted opacity-50">↑↓</span></template>
                                                    <template x-if="sortField === 'name'">
                                                        <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th>معلومات التواصل</th>
                                        <th>المنطقة الزمنية</th>

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
                                        <th>انشئ في</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    @forelse($this->branchesList as $branch)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chk_child"
                                                    value="{{ $branch->id }}"
                                                    x-model="selectedIds">
                                            </div>
                                        </th>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a class="edit-item-btn" href="javascript:void(0);" @click="$wire.editBranch({{ $branch->id }}).then(() => showModal = true)"><i class="ri-pencil-fill align-bottom text-muted"></i></a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Delete">
                                                    <a class="remove-item-btn" href="javascript:void(0);" @click="$wire.confirmDelete({{ $branch->id }}).then(() => showDeleteModal = true)">
                                                        <i class="ri-delete-bin-fill align-bottom text-muted"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="code"><span class="badge bg-light text-body fs-12">{{ $branch->code ?? '-' }}</span></td>
                                        <td class="name">
                                            <div class="d-flex align-items-center">
                                                @if($branch->logo)
                                                <img src="{{ asset('storage/' . $branch->logo) }}" alt="" class="avatar-xs rounded-circle me-2">
                                                @else
                                                <div class="avatar-xs rounded-circle bg-primary-subtle text-primary d-flex justify-content-center align-items-center me-2">
                                                    <i class="ri-store-2-line fs-14"></i>
                                                </div>
                                                @endif
                                                <div>
                                                    <h5 class="fs-14 my-1 fw-medium">{{ $branch->name }}</h5>
                                                    <span class="text-muted mb-0"><i class="ri-building-line me-1 align-bottom"></i> {{ $branch->company->name ?? 'غير محدد' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="contact_info">
                                            <div class="d-flex flex-column">
                                                @if($branch->mobile) <span class="fs-13"><i class="ri-phone-line text-muted me-1"></i> <span dir="ltr">{{ $branch->mobile }}</span></span> @endif
                                                @if($branch->email) <span class="fs-13"><i class="ri-mail-line text-muted me-1"></i> {{ $branch->email }}</span> @endif
                                                @if(!$branch->mobile && !$branch->email) <span class="text-muted">-</span> @endif
                                            </div>
                                        </td>
                                        <td class="timezone"><span class="badge bg-secondary-subtle text-secondary">{{ $branch->timezone }}</span></td>
                                        <td class="status">
                                            <div class="form-check form-switch form-switch-md mb-2" dir="ltr">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    wire:click="toggleStatus({{ $branch->id }})"
                                                    {{ $branch->status->value === 1 ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="created_at">{{ optional($branch->created_at)->diffForHumans() }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="noresult">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">عذراً! لم يتم العثور على نتائج</h5>
                                                    <p class="text-muted mb-0">لم نعثر على أي فروع مطابقة لبحثك.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{$this->branchesList->links('livewire::custom-pagination-links')}}

                            <div wire:ignore.self
                                class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                :class="{ 'show d-block': showModal }" :aria-hidden="!showModal"
                                x-show="showModal" x-transition.opacity>
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header bg-light p-3">
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                @if($form->branch)
                                                <i class="ri-edit-line me-2 text-warning"></i> تعديل بيانات الفرع
                                                @else
                                                <i class="ri-store-2-line me-2 text-success"></i> إضافة فرع جديد
                                                @endif
                                            </h5>
                                            <button type="button" class="btn-close" aria-label="Close" @click="showModal = false; $wire.cancel()"></button>
                                        </div>
                                        <form wire:submit.prevent="submitBranch" autocomplete="off">
                                            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                                <div class="row g-3">

                                                    <div class="col-12">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-2 pb-1 border-bottom">
                                                            <i class="ri-information-line me-1"></i> البيانات الأساسية
                                                        </h6>
                                                    </div>

                                                    {{-- عرض رسائل الخطأ للحقول المخفية --}}
                                                    @if($errors->has('form.code') || $errors->has('form.slug'))
                                                    <div class="col-12">
                                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                            <i class="ri-error-warning-line me-2 align-middle"></i>
                                                            <strong>تنبيه:</strong> حدث خطأ في البيانات التلقائية:
                                                            <ul class="mb-0 mt-1">
                                                                @error('form.code') <li>{{ $message }}</li> @enderror
                                                                @error('form.slug') <li>{{ $message }}</li> @enderror
                                                            </ul>
                                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    <div class="col-lg-4">
                                                        <label for="form-company_id" class="form-label">الشركة <span class="text-danger">*</span></label>
                                                        <select id="form-company_id" class="form-select @error('form.company_id') is-invalid @enderror" wire:model="form.company_id">
                                                            <option value="">اختر الشركة...</option>
                                                            @foreach(\App\Models\Company::all() as $company)
                                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('form.company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-4 d-none">
                                                        <label for="form-code" class="form-label">كود الفرع <small class="text-muted">(تلقائي)</small></label>
                                                        <input type="text" id="form-code" class="form-control @error('form.code') is-invalid @enderror"
                                                            wire:model.blur="form.code" placeholder="BR-0001" dir="ltr" readonly />
                                                        @error('form.code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-4 d-none">
                                                        <label for="form-slug" class="form-label">رابط الفرع (Slug) <small class="text-muted">(تلقائي)</small> <span class="text-danger">*</span></label>
                                                        <input type="text" id="form-slug" class="form-control @error('form.slug') is-invalid @enderror"
                                                            wire:model.blur="form.slug" placeholder="riyadh-branch" dir="ltr" readonly />
                                                        @error('form.slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-name" class="form-label">اسم الفرع (بالعربية) <span class="text-danger">*</span></label>
                                                        <input type="text" id="form-name" class="form-control @error('form.name') is-invalid @enderror"
                                                            wire:model.blur="form.name" placeholder="مثال: فرع الرياض" />
                                                        @error('form.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-name_en" class="form-label">اسم الفرع (بالإنجليزية)</label>
                                                        <input type="text" id="form-name_en" class="form-control @error('form.name_en') is-invalid @enderror"
                                                            wire:model.blur="form.name_en" placeholder="مثال: Riyadh Branch" />
                                                        @error('form.name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-12 mt-4">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-2 pb-1 border-bottom">
                                                            <i class="ri-file-text-line me-1"></i> البيانات القانونية والضريبية
                                                        </h6>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-commercial_register" class="form-label">السجل التجاري</label>
                                                        <input type="text" id="form-commercial_register" class="form-control @error('form.commercial_register') is-invalid @enderror"
                                                            wire:model.blur="form.commercial_register" placeholder="رقم السجل التجاري للفرع" />
                                                        @error('form.commercial_register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="form-tax_number" class="form-label">الرقم الضريبي</label>
                                                        <input type="text" id="form-tax_number" class="form-control @error('form.tax_number') is-invalid @enderror"
                                                            wire:model.blur="form.tax_number" placeholder="الرقم الضريبي المميز" />
                                                        @error('form.tax_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-12 mt-4">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-2 pb-1 border-bottom">
                                                            <i class="ri-map-pin-line me-1"></i> بيانات العنوان والموقع
                                                        </h6>
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <label for="form-country_id" class="form-label">الدولة</label>
                                                        <select id="form-country_id" class="form-select @error('form.country_id') is-invalid @enderror" wire:model="form.country_id">
                                                            <option value="">اختر الدولة...</option>
                                                            @foreach(\App\Models\Country::all() as $country)
                                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('form.country_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <label for="form-state" class="form-label">المنطقة/المحافظة</label>
                                                        <input type="text" id="form-state" class="form-control @error('form.state') is-invalid @enderror" wire:model.blur="form.state" />
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <label for="form-city" class="form-label">المدينة</label>
                                                        <input type="text" id="form-city" class="form-control @error('form.city') is-invalid @enderror" wire:model.blur="form.city" />
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <label for="form-district" class="form-label">الحي</label>
                                                        <input type="text" id="form-district" class="form-control @error('form.district') is-invalid @enderror" wire:model.blur="form.district" />
                                                    </div>

                                                    <div class="col-lg-8">
                                                        <label for="form-street_address" class="form-label">العنوان التفصيلي (الشارع)</label>
                                                        <input type="text" id="form-street_address" class="form-control @error('form.street_address') is-invalid @enderror" wire:model.blur="form.street_address" />
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label for="form-building_number" class="form-label">رقم المبنى</label>
                                                        <input type="text" id="form-building_number" class="form-control @error('form.building_number') is-invalid @enderror" wire:model.blur="form.building_number" />
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label for="form-postal_code" class="form-label">الرمز البريدي</label>
                                                        <input type="text" id="form-postal_code" class="form-control @error('form.postal_code') is-invalid @enderror" wire:model.blur="form.postal_code" dir="ltr" />
                                                    </div>

                                                    <div class="col-12 mt-4">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-2 pb-1 border-bottom">
                                                            <i class="ri-settings-4-line me-1"></i> الإعدادات وبيانات التواصل
                                                        </h6>
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <label for="form-timezone" class="form-label">المنطقة الزمنية <span class="text-danger">*</span></label>
                                                        <select id="form-timezone" class="form-select @error('form.timezone') is-invalid @enderror" wire:model="form.timezone" dir="ltr">
                                                            <option value="Asia/Riyadh">Asia/Riyadh</option>
                                                            <option value="Asia/Aden">Asia/Aden</option>
                                                            <option value="Africa/Cairo">Africa/Cairo</option>
                                                            <option value="Asia/Dubai">Asia/Dubai</option>
                                                        </select>
                                                        @error('form.timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <label for="form-currency_id" class="form-label">عملة الفرع المخصصة</label>
                                                        <select id="form-currency_id" class="form-select @error('form.currency_id') is-invalid @enderror" wire:model="form.currency_id">
                                                            <option value="">اختر عملة الشركة...</option>
                                                            @foreach(\App\Models\Currency::all() as $currency)
                                                            <option value="{{ $currency->id }}">{{ $currency->name }} ({{ $currency->code }})</option>
                                                            @endforeach
                                                        </select>
                                                        @error('form.currency_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <label for="form-status" class="form-label">حالة الفرع <span class="text-danger">*</span></label>
                                                        <select id="form-status" class="form-select @error('form.status') is-invalid @enderror" wire:model.blur="form.status">
                                                            <option value="1">مفعل</option>
                                                            <option value="0">غير مفعل</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <label for="form-mobile" class="form-label">رقم الجوال</label>
                                                        <input type="text" id="form-mobile" class="form-control @error('form.mobile') is-invalid @enderror" wire:model.blur="form.mobile" dir="ltr" />
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <label for="form-phone" class="form-label">الهاتف الأرضي</label>
                                                        <input type="text" id="form-phone" class="form-control @error('form.phone') is-invalid @enderror" wire:model.blur="form.phone" dir="ltr" />
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <label for="form-email" class="form-label">البريد الإلكتروني</label>
                                                        <input type="email" id="form-email" class="form-control @error('form.email') is-invalid @enderror" wire:model.blur="form.email" dir="ltr" />
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="modal-footer mt-3">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="button" class="btn btn-light" @click="showModal = false; $wire.cancel()">
                                                        <i class="ri-close-line me-1"></i> إلغاء
                                                    </button>
                                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                                        <span wire:loading.remove wire:target="submitBranch">
                                                            @if($form->branch)
                                                            <i class="ri-save-line me-1"></i> تحديث البيانات
                                                            @else
                                                            <i class="ri-save-line me-1"></i> حفظ الفرع
                                                            @endif
                                                        </span>
                                                        <span wire:loading wire:target="submitBranch">
                                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> جاري الحفظ...
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div wire:ignore.self
                                id="deleteRecordModal" class="modal fade zoomIn" tabindex="-1" aria-labelledby="deleteRecordLabel"
                                x-show="showDeleteModal" :class="{ 'show d-block': showDeleteModal }" :aria-hidden="!showDeleteModal">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn-close" aria-label="Close" @click="showDeleteModal = false"></button>
                                        </div>
                                        <div class="modal-body p-5 text-center">
                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                                            <div class="mt-4 text-center">
                                                <h4 class="fs-semibold">هل أنت متأكد من حذف هذا الفرع؟</h4>
                                                <p class="text-muted fs-14 mb-4 pt-1">سيتم نقل الفرع لسلة المهملات، ولن يتمكن المستخدمون من إصدار فواتير باسمه.</p>
                                                <div class="hstack gap-2 justify-content-center remove">
                                                    <button class="btn btn-light" @click="showDeleteModal = false">
                                                        <i class="ri-close-line me-1 align-middle"></i> إلغاء
                                                    </button>
                                                    <button class="btn btn-danger" wire:click="deleteBranch" @click="showDeleteModal = false">
                                                        <i class="ri-delete-bin-fill me-1"></i> نعم، احذف!
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- تأكد من وجود ملف offcanvas مخصص للفروع --}}
                            @include('partials.backend.branches.offcanvas')

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
                window.dispatchEvent(new CustomEvent('close-branch-modal'));
            });
        });
    </script>
    @endpush
</div>