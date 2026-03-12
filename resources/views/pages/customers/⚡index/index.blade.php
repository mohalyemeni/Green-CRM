<div x-data="{
    selectedIds: @entangle('selectedIds'),
    sortField: @entangle('sortField'),
    sortDirection: @entangle('sortDirection'),
    showModal: false,
    showDeleteModal: false,
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
    x-on:open-customer-modal.window="showModal = true"
    x-on:close-customer-modal.window="showModal = false; $wire.cancel()"
    x-on:open-delete-modal.window="showDeleteModal = true"
    x-on:close-delete-modal.window="showDeleteModal = false">
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
                                    placeholder="ابحث عن العميل، الإيميل، أو الرقم..."
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
                                    <button type="button" class="btn btn-info" data-bs-toggle="offcanvas" href="#offcanvasExample"><i class="ri-filter-3-line align-bottom me-1"></i> Fliters</button>
                                    <button type="button" class="btn btn-success add-btn" @click="$wire.cancel(); showModal = true" id="create-btn"><i class="ri-add-line align-bottom me-1"></i> إضافة عميل</button>
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
            <div class="card" id="leadsList">
                <div class="card-header border-0">

                    <div class="row g-4 align-items-center">
                        <div class="col-sm-10">
                            <h5 class="card-title mb-0">إدارة بيانات العملاء</h5>
                            <p><small>عرض وإدارة جميع بيانات العملاء المسجلين في النظام.</small></p>
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
                            <table class="table align-middle table-nowrap mb-0" id="customerTable">
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
                                        <th @click="sortBy('customer_number')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>المعرف</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'customer_number'">
                                                        <span class="text-muted opacity-50">↑↓</span>
                                                    </template>
                                                    <template x-if="sortField === 'customer_number'">
                                                        <span>
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th @click="sortBy('name')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>اسم العميل</span>
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

                                        <th @click="sortBy('country')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>الدولة</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'country'">
                                                        <span class="text-muted opacity-50">↑↓</span>
                                                    </template>
                                                    <template x-if="sortField === 'country'">
                                                        <span>
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>
                                        <th @click="sortBy('email')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>الايميل</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'email'">
                                                        <span class="text-muted opacity-50">↑↓</span>
                                                    </template>
                                                    <template x-if="sortField === 'email'">
                                                        <span>
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>
                                        <th>رقم الهاتف</th>
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
                                    @forelse($this->customers as $customer)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chk_child"
                                                    value="{{ $customer->id }}"
                                                    x-model="selectedIds">
                                            </div>
                                        </th>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a class="edit-item-btn" href="javascript:void(0);" @click="$wire.editCustomer({{ $customer->id }}).then(() => showModal = true)"><i class="ri-pencil-fill align-bottom text-muted"></i></a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Delete">
                                                    <a class="remove-item-btn" href="javascript:void(0);" @click="$wire.confirmDelete({{ $customer->id }}).then(() => showDeleteModal = true)">
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
                                        <td class="customer_number"><a href="javascript:void(0);" class="fw-medium link-primary">{{ $customer->customer_number }}</a></td>
                                        <td class="name">{{$customer->name}}</td>
                                        <td class="country">{{$customer->country}}</td>
                                        <td class="email">{{$customer->email}}</td>
                                        <td class="phone">{{$customer->mobile}}</td>
                                        <td class="status"><span class="badge bg-{{ $customer->status->color() }}-subtle text-{{ $customer->status->color() }} text-uppercase"><small>{{ $customer->status->label() }}</small></span></td>
                                        <td class="created_at">{{ $customer->created_at->diffForHumans() }}</td>

                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="noresult">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">عذراً! لم يتم العثور على نتائج</h5>
                                                    <p class="text-muted mb-0">لقد بحثنا في أكثر من 150 عميل محتمل، ولم نعثر على أي نتائج مطابقة لبحثك.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{$this->customers->links('livewire::custom-pagination-links')}}

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
                                                @if($form->customer)
                                                <i class="ri-edit-line me-2 text-warning"></i> تعديل بيانات العميل
                                                @else
                                                <i class="ri-user-add-line me-2 text-success"></i> إضافة عميل جديد
                                                @endif
                                            </h5>
                                            <button type="button" class="btn-close" aria-label="Close" @click="showModal = false; $wire.cancel()"></button>
                                        </div>
                                        <form wire:submit.prevent="submitCustomer" autocomplete="off">
                                            <div class="modal-body">
                                                <div class="row g-3">

                                                    {{-- ===== قسم: البيانات الأساسية ===== --}}
                                                    <div class="col-12">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-2 pb-1 border-bottom">
                                                            <i class="ri-user-line me-1"></i> البيانات الأساسية
                                                        </h6>
                                                    </div>

                                                    {{-- الاسم --}}
                                                    <div class="col-lg-6">
                                                        <label for="form-name" class="form-label">اسم العميل <span class="text-danger">*</span></label>
                                                        <input type="text" id="form-name" class="form-control @error('form.name') is-invalid @enderror"
                                                            wire:model="form.name"
                                                            placeholder="أدخل اسم العميل كاملاً" />
                                                        @error('form.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- الهوية الوطنية --}}
                                                    <div class="col-lg-6">
                                                        <label for="form-national_id" class="form-label">الهوية الوطنية</label>
                                                        <input type="text" id="form-national_id" class="form-control @error('form.national_id') is-invalid @enderror"
                                                            wire:model="form.national_id"
                                                            placeholder="أدخل رقم الهوية الوطنية" />
                                                        @error('form.national_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- العمر --}}
                                                    <div class="col-lg-4">
                                                        <label for="form-age" class="form-label">العمر</label>
                                                        <input type="number" id="form-age" class="form-control @error('form.age') is-invalid @enderror"
                                                            wire:model="form.age"
                                                            min="1" max="120" placeholder="العمر" />
                                                        @error('form.age') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- الجنس --}}
                                                    <div class="col-lg-4">
                                                        <label for="form-gender" class="form-label">الجنس</label>
                                                        <select id="form-gender" class="form-select @error('form.gender') is-invalid @enderror"
                                                            wire:model="form.gender">
                                                            <option value="">-- اختر الجنس --</option>
                                                            <option value="1">ذكر</option>
                                                            <option value="2">أنثى</option>
                                                        </select>
                                                        @error('form.gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- الحالة --}}
                                                    <div class="col-lg-4">
                                                        <label for="form-status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                                        <select id="form-status" class="form-select @error('form.status') is-invalid @enderror"
                                                            wire:model="form.status">
                                                            <option value="1">مفعل</option>
                                                            <option value="2">غير مفعل</option>
                                                            <option value="3">موقوف مؤقتاً</option>
                                                        </select>
                                                        @error('form.status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- ===== قسم: بيانات التواصل ===== --}}
                                                    <div class="col-12 mt-2">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-2 pb-1 border-bottom">
                                                            <i class="ri-phone-line me-1"></i> بيانات التواصل
                                                        </h6>
                                                    </div>

                                                    {{-- الجوال --}}
                                                    <div class="col-lg-6">
                                                        <label for="form-mobile" class="form-label">رقم الجوال</label>
                                                        <input type="tel" id="form-mobile" class="form-control @error('form.mobile') is-invalid @enderror"
                                                            wire:model="form.mobile"
                                                            placeholder="05XXXXXXXX" />
                                                        @error('form.mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- البريد الإلكتروني --}}
                                                    <div class="col-lg-6">
                                                        <label for="form-email" class="form-label">البريد الإلكتروني</label>
                                                        <input type="email" id="form-email" class="form-control @error('form.email') is-invalid @enderror"
                                                            wire:model="form.email"
                                                            placeholder="example@domain.com" />
                                                        @error('form.email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- ===== قسم: بيانات العنوان ===== --}}
                                                    <div class="col-12 mt-2">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-2 pb-1 border-bottom">
                                                            <i class="ri-map-pin-line me-1"></i> بيانات العنوان
                                                        </h6>
                                                    </div>

                                                    {{-- العنوان العام --}}
                                                    <div class="col-12">
                                                        <label for="form-general_address" class="form-label">العنوان العام</label>
                                                        <input type="text" id="form-general_address" class="form-control @error('form.general_address') is-invalid @enderror"
                                                            wire:model="form.general_address"
                                                            placeholder="أدخل العنوان العام" />
                                                        @error('form.general_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- رقم المبنى --}}
                                                    <div class="col-lg-4">
                                                        <label for="form-building_number" class="form-label">رقم المبنى</label>
                                                        <input type="text" id="form-building_number" class="form-control @error('form.building_number') is-invalid @enderror"
                                                            wire:model="form.building_number"
                                                            placeholder="رقم المبنى" />
                                                        @error('form.building_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- اسم الشارع --}}
                                                    <div class="col-lg-4">
                                                        <label for="form-street_name" class="form-label">اسم الشارع</label>
                                                        <input type="text" id="form-street_name" class="form-control @error('form.street_name') is-invalid @enderror"
                                                            wire:model="form.street_name"
                                                            placeholder="اسم الشارع" />
                                                        @error('form.street_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- الحي --}}
                                                    <div class="col-lg-4">
                                                        <label for="form-district" class="form-label">الحي</label>
                                                        <input type="text" id="form-district" class="form-control @error('form.district') is-invalid @enderror"
                                                            wire:model="form.district"
                                                            placeholder="اسم الحي" />
                                                        @error('form.district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- المدينة --}}
                                                    <div class="col-lg-6">
                                                        <label for="form-city" class="form-label">المدينة</label>
                                                        <input type="text" id="form-city" class="form-control @error('form.city') is-invalid @enderror"
                                                            wire:model="form.city"
                                                            placeholder="المدينة" />
                                                        @error('form.city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- الدولة --}}
                                                    <div class="col-lg-6">
                                                        <label for="form-country" class="form-label">الدولة</label>
                                                        <input type="text" id="form-country" class="form-control @error('form.country') is-invalid @enderror"
                                                            wire:model="form.country"
                                                            placeholder="الدولة" />
                                                        @error('form.country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- ===== قسم: البيانات المالية والتجارية ===== --}}
                                                    <div class="col-12 mt-2">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-2 pb-1 border-bottom">
                                                            <i class="ri-money-dollar-circle-line me-1"></i> البيانات المالية
                                                        </h6>
                                                    </div>

                                                    {{-- الرقم الضريبي --}}
                                                    <div class="col-lg-4">
                                                        <label for="form-tax_number" class="form-label">الرقم الضريبي</label>
                                                        <input type="text" id="form-tax_number" class="form-control @error('form.tax_number') is-invalid @enderror"
                                                            wire:model="form.tax_number"
                                                            placeholder="الرقم الضريبي" />
                                                        @error('form.tax_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- طريقة التعامل --}}
                                                    <div class="col-lg-4">
                                                        <label for="form-dealing_method" class="form-label">طريقة التعامل</label>
                                                        <select id="form-dealing_method" class="form-select @error('form.dealing_method') is-invalid @enderror"
                                                            wire:model="form.dealing_method">
                                                            <option value="">-- اختر الطريقة --</option>
                                                            <option value="cash">كاش</option>
                                                            <option value="credit">آجل</option>
                                                        </select>
                                                        @error('form.dealing_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- حد الدين --}}
                                                    <div class="col-lg-4">
                                                        <label for="form-credit_limit" class="form-label">حد الدين</label>
                                                        <div class="input-group">
                                                            <input type="number" id="form-credit_limit" class="form-control @error('form.credit_limit') is-invalid @enderror"
                                                                wire:model="form.credit_limit"
                                                                min="0" step="0.01" placeholder="0.00" />
                                                            <span class="input-group-text">ر.س</span>
                                                        </div>
                                                        @error('form.credit_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    {{-- ===== الملاحظات ===== --}}
                                                    <div class="col-12 mt-1">
                                                        <label for="form-notes" class="form-label">ملاحظات</label>
                                                        <textarea id="form-notes" class="form-control @error('form.notes') is-invalid @enderror"
                                                            wire:model="form.notes"
                                                            rows="3" placeholder="أي ملاحظات إضافية على العميل..."></textarea>
                                                        @error('form.notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                </div>{{-- end row --}}
                                            </div>{{-- end modal-body --}}

                                            <div class="modal-footer">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="button" class="btn btn-light" @click="showModal = false; $wire.cancel()">
                                                        <i class="ri-close-line me-1"></i> إلغاء
                                                    </button>
                                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                                        <span wire:loading.remove wire:target="saveCustomer,updateCustomer">
                                                            @if($form->customer)
                                                            <i class="ri-save-line me-1"></i> تحديث البيانات
                                                            @else
                                                            <i class="ri-save-line me-1"></i> حفظ العميل
                                                            @endif
                                                        </span>
                                                        <span wire:loading wire:target="saveCustomer,updateCustomer">
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
                                                <h4 class="fs-semibold">هل أنت متأكد من حذف هذا العميل؟</h4>
                                                <p class="text-muted fs-14 mb-4 pt-1">سيتم حذف جميع بيانات العميل من قاعدة البيانات بشكل نهائي.</p>
                                                <div class="hstack gap-2 justify-content-center remove">
                                                    <button class="btn btn-light" @click="showDeleteModal = false">
                                                        <i class="ri-close-line me-1 align-middle"></i> إلغاء
                                                    </button>
                                                    <button class="btn btn-danger" wire:click="deleteCustomer" @click="showDeleteModal = false">
                                                        <i class="ri-delete-bin-fill me-1"></i> نعم، احذف!
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end delete modal-->


                            @include('partials.backend.customers.offcanvas')

                        </div>
                    </div>

                </div>
                <!--end col-->
            </div>
        </div>

        {{-- Customer Form Modal Backdrop --}}
        <div class="modal-backdrop fade show" x-show="showModal" style="display:none;"></div>

        {{-- Delete Confirmation Modal Backdrop --}}
        <div class="modal-backdrop fade show" x-show="showDeleteModal" style="display:none;"></div>
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
                window.dispatchEvent(new CustomEvent('close-customer-modal'));
            });

            // Close offcanvas when Livewire dispatches close-offcanvas
            Livewire.on('close-offcanvas', () => {
                const offcanvasElement = document.getElementById('offcanvasExample');
                if (offcanvasElement) {
                    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (offcanvasInstance) {
                        offcanvasInstance.hide();
                    }
                }
            });
        });
    </script>
    @endpush