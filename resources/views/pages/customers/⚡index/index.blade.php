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
    x-on:open-customer-modal.window="showModal = true"
    x-on:close-customer-modal.window="showModal = false; $wire.cancel()"
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
                                    <button type="button" class="btn btn-info" @click="showOffcanvas = true">
                                        <i class="ri-filter-3-line align-bottom me-1"></i> تصفية
                                    </button>
                                    <button type="button" class="btn btn-success add-btn" @click="$wire.cancel(); showModal = true" id="create-btn">
                                        <i class="ri-add-line align-bottom me-1"></i> إضافة عميل
                                    </button>
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
            <div class="card" id="customersList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-10">
                            <h5 class="card-title mb-0">إدارة بيانات العملاء</h5>
                            <p class="text-muted mb-0"><small>عرض وإدارة جميع بيانات العملاء المسجلين في النظام.</small></p>
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
                            <table class="table align-middle table-nowrap mb-0" id="customerTable">
                                <thead class="table-light text-muted">
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
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span>
                                                            <span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
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
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span>
                                                            <span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
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
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span>
                                                            <span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
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
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span>
                                                            <span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th @click="sortBy('mobile')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>رقم الهاتف</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'mobile'">
                                                        <span class="text-muted opacity-50">↑↓</span>
                                                    </template>
                                                    <template x-if="sortField === 'mobile'">
                                                        <span>
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span>
                                                            <span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th @click="sortBy('status')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>الحالة</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'status'">
                                                        <span class="text-muted opacity-50">↑↓</span>
                                                    </template>
                                                    <template x-if="sortField === 'status'">
                                                        <span>
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span>
                                                            <span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th @click="sortBy('created_at')" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>انشئ في</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'created_at'">
                                                        <span class="text-muted opacity-50">↑↓</span>
                                                    </template>
                                                    <template x-if="sortField === 'created_at'">
                                                        <span>
                                                            <span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span>
                                                            <span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span>
                                                        </span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>
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
                                                    <a class="edit-item-btn" href="javascript:void(0);" @click="$wire.editCustomer({{ $customer->id }}).then(() => showModal = true)">
                                                        <i class="ri-pencil-fill align-bottom text-muted"></i>
                                                    </a>
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
                                        <td class="customer_number">
                                            <a href="javascript:void(0);" class="fw-medium link-primary">{{ $customer->customer_number }}</a>
                                        </td>
                                        <td class="name">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xxs flex-shrink-0 me-2">
                                                    <span class="avatar-title rounded-circle fs-10" style="background-color: {{ optional($customer->status)->color() }}"></span>
                                                </div>
                                                <div>
                                                    <h5 class="fs-14 my-1 fw-medium">{{ $customer->name }}</h5>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="country">{{ $customer->country ?? '-' }}</td>
                                        <td class="email">{{ $customer->email ?? '-' }}</td>
                                        <td class="phone">{{ $customer->mobile }}</td>

                                        {{-- ===== زر التفعيل / الإلغاء (التعديل الجديد) ===== --}}
                                        <td class="status">
                                            <div class="form-check form-switch form-switch-success form-switch-md mb-0" dir="ltr" style="display: flex; justify-content: flex-end; align-items: center;">
                                                <input class="form-check-input me-2" type="checkbox" role="switch"
                                                    id="statusSwitch_{{ $customer->id }}"
                                                    wire:click="toggleStatus({{ $customer->id }})"
                                                    @if(optional($customer->status)->value === 1) checked @endif>
                                                <label class="form-check-label mb-0 text-{{ optional($customer->status)->color() }}" for="statusSwitch_{{ $customer->id }}" dir="rtl" style="min-width: 60px;">
                                                    <small class="fw-semibold">{{ optional($customer->status)->label() }}</small>
                                                </label>
                                            </div>
                                        </td>

                                        <td class="created_at">{{ optional($customer->created_at)->diffForHumans() }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9">
                                            <div class="noresult py-5">
                                                <div class="text-center">
                                                    <i class="ri-search-line display-5 text-success mb-3 d-block"></i>
                                                    <h5 class="mt-2 fw-bold text-dark">عذراً! لم يتم العثور على نتائج</h5>
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
                                    <div class="modal-content border-0 overflow-hidden">
                                        <div class="modal-header bg-light p-3 border-bottom">
                                            <h5 class="modal-title d-flex align-items-center" id="exampleModalLabel">
                                                @if($form->customer)
                                                <i class="ri-edit-line me-2 text-warning fs-20"></i> تعديل بيانات العميل
                                                @else
                                                <i class="ri-user-add-line me-2 text-primary fs-20"></i> إضافة عميل جديد
                                                @endif
                                            </h5>
                                            <button type="button" class="btn-close" aria-label="Close" @click="showModal = false; $wire.cancel()"></button>
                                        </div>

                                        <form wire:submit.prevent="submitCustomer" autocomplete="off">
                                            <div class="modal-body p-4">

                                                <div class="mb-4">
                                                    <h6 class="fs-14 fw-bold text-muted mb-3 border-bottom pb-2">
                                                        <i class="ri-information-line me-1 align-bottom"></i> البيانات الأساسية
                                                    </h6>
                                                    <div class="row g-3">
                                                        <div class="col-lg-6">
                                                            <label class="form-label fw-medium">
                                                                رقم العميل
                                                                <span class="text-muted fs-11 fw-normal">(اختياري - يُولَّد تلقائياً)</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-light"><i class="ri-hashtag"></i></span>
                                                                <input type="text"
                                                                    class="form-control @error('form.customer_number') is-invalid @enderror"
                                                                    wire:model.blur="form.customer_number"
                                                                    placeholder="مثال: CUST-001 (أو اتركه فارغاً)"
                                                                    @if(!$form->customer) dir="ltr" @endif />
                                                                @error('form.customer_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                            </div>
                                                            @if(!$form->customer)
                                                            <div class="form-text text-muted"><i class="ri-information-line"></i> إذا تُرك فارغاً سيتم توليده تلقائياً</div>
                                                            @endif
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <label class="form-label fw-medium">اسم العميل <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('form.name') is-invalid @enderror" wire:model.blur="form.name" placeholder="الاسم الكامل" />
                                                            @error('form.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <label class="form-label fw-medium">الجنس</label>
                                                            <select class="form-select @error('form.gender') is-invalid @enderror" wire:model.blur="form.gender">
                                                                <option value="">اختر الجنس</option>
                                                                <option value="1">ذكر</option>
                                                                <option value="2">أنثى</option>
                                                            </select>
                                                            @error('form.gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <label class="form-label fw-medium">حالة التفعيل <span class="text-danger">*</span></label>
                                                            <select class="form-select @error('form.status') is-invalid @enderror" wire:model.blur="form.status">
                                                                <option value="1">مفعّل</option>
                                                                <option value="2">غير مفعّل</option>
                                                                <option value="3">معلّق</option>
                                                            </select>
                                                            @error('form.status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-4 bg-light p-3 rounded-3 border">
                                                    <h6 class="fs-14 fw-bold text-muted mb-3">
                                                        <i class="ri-contacts-book-2-line me-1 align-bottom"></i> معلومات التواصل
                                                    </h6>
                                                    <div class="row g-3">
                                                        <div class="col-lg-4">
                                                            <label class="form-label fw-medium">رقم الموبايل <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('form.mobile') is-invalid @enderror" wire:model.blur="form.mobile" dir="ltr" placeholder="05XXXXXXXX" />
                                                            @error('form.mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>

                                                        <div class="col-lg-4">
                                                            <label class="form-label fw-medium">رقم الهاتف</label>
                                                            <input type="text" class="form-control @error('form.phone') is-invalid @enderror" wire:model.blur="form.phone" dir="ltr" placeholder="رقم الهاتف" />
                                                            @error('form.phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>

                                                        <div class="col-lg-4">
                                                            <label class="form-label fw-medium">رقم الوتس</label>
                                                            <input type="text" class="form-control @error('form.whatsapp') is-invalid @enderror" wire:model.blur="form.whatsapp" dir="ltr" placeholder="رقم الواتس" />
                                                            @error('form.whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>

                                                        <div class="col-lg-12">
                                                            <label class="form-label fw-medium">البريد الإلكتروني</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white border-end-0"><i class="ri-mail-line"></i></span>
                                                                <input type="email" class="form-control border-start-0 @error('form.email') is-invalid @enderror" wire:model.blur="form.email" dir="ltr" placeholder="example@domain.com" />
                                                                @error('form.email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <h6 class="fs-14 fw-bold text-muted mb-3 border-bottom pb-2">
                                                        <i class="ri-map-pin-line me-1 align-bottom"></i> بيانات العنوان
                                                    </h6>
                                                    <div class="row g-3">
                                                        <div class="col-lg-12">
                                                            <label class="form-label fw-medium">العنوان العام</label>
                                                            <input type="text" class="form-control @error('form.general_address') is-invalid @enderror" wire:model.blur="form.general_address" placeholder="المدينة، الحي، الشارع..." />
                                                            @error('form.general_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>

                                                        <div class="col-lg-4">
                                                            <label class="form-label fw-medium">الحي</label>
                                                            <input type="text" class="form-control @error('form.district') is-invalid @enderror" wire:model.blur="form.district" placeholder="اسم الحي" />
                                                            @error('form.district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>

                                                        <div class="col-lg-4">
                                                            <label class="form-label fw-medium">اسم الشارع</label>
                                                            <input type="text" class="form-control @error('form.street_name') is-invalid @enderror" wire:model.blur="form.street_name" placeholder="اسم الشارع" />
                                                            @error('form.street_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>

                                                        <div class="col-lg-4">
                                                            <label class="form-label fw-medium">رقم المبنى</label>
                                                            <input type="text" class="form-control @error('form.building_number') is-invalid @enderror" wire:model.blur="form.building_number" placeholder="رقم المبنى" />
                                                            @error('form.building_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <label class="form-label fw-medium">المدينة</label>
                                                            <input type="text" class="form-control @error('form.city') is-invalid @enderror" wire:model.blur="form.city" placeholder="المدينة" />
                                                            @error('form.city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <label class="form-label fw-medium">الدولة</label>
                                                            <select class="form-control @error('form.country') is-invalid @enderror" wire:model.blur="form.country">
                                                                <option value="">كل الدول</option>
                                                                @foreach($this->countries() as $country)
                                                                <option value="{{ $country }}">{{ $country }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('form.country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>

                                                        <div class="col-12 mt-3">
                                                            <label class="form-label fw-medium">ملاحظات</label>
                                                            <textarea class="form-control @error('form.notes') is-invalid @enderror" wire:model="form.notes" rows="2" placeholder="أضف أي ملاحظات إضافية هنا..."></textarea>
                                                            @error('form.notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="modal-footer bg-light border-top mt-3">
                                                <div class="hstack gap-2 justify-content-end w-100">
                                                    <button type="button" class="btn btn-ghost-danger" @click="showModal = false; $wire.cancel()">
                                                        <i class="ri-close-line align-bottom me-1"></i> إلغاء
                                                    </button>
                                                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="submitCustomer">
                                                        <span wire:loading.remove wire:target="submitCustomer">
                                                            @if($form->customer)
                                                            <i class="ri-save-3-line align-bottom me-1"></i> تحديث البيانات
                                                            @else
                                                            <i class="ri-save-3-line align-bottom me-1"></i> حفظ بيانات العميل
                                                            @endif
                                                        </span>
                                                        <span wire:loading wire:target="submitCustomer">
                                                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> جاري الحفظ...
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
                            @include('partials.backend.customers.offcanvas')

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-backdrop"
            style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"
            x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
        </div>

        <div class="modal-backdrop"
            style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"
            x-show="showDeleteModal"
            x-transition.opacity>
        </div>

        <div class="offcanvas-backdrop"
            style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"
            x-show="showOffcanvas"
            x-transition.opacity
            @click="showOffcanvas = false">
        </div>

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
        });
    </script>
    @endpush
</div>