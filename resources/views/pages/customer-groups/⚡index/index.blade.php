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
    x-on:open-group-modal.window="showModal = true"
    x-on:close-group-modal.window="showModal = false; $wire.cancel()"
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
                                    placeholder="ابحث عن اسم المجموعة، أو الكود..."
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
                                            @click="if(confirm('هل أنت متأكد من حذف المجموعات المحددة؟')) $wire.deleteMultiple()">
                                            <i class="ri-delete-bin-2-line"></i> (<span x-text="selectedIds.length"></span>)
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-info" @click="showOffcanvas = true"><i class="ri-filter-3-line align-bottom me-1"></i> تصفية</button>
                                    <button type="button" class="btn btn-success add-btn" @click="$wire.cancel(); showModal = true" id="create-btn"><i class="ri-add-line align-bottom me-1"></i> إضافة مجموعة</button>
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
            <div class="card" id="groupsList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-10">
                            <h5 class="card-title mb-0">إدارة مجموعات العملاء</h5>
                            <p><small>تنظيم العملاء في مجموعات مخصصة لتسهيل إدارتهم.</small></p>
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
                            <table class="table align-middle table-nowrap mb-0" id="groupTable">
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
                                                <span>الكود</span>
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
                                                <span>اسم المجموعة</span>
                                                <span class="fs-11 ms-1" style="width: 20px; display: inline-block; text-align: center;">
                                                    <template x-if="sortField !== 'name'"><span class="text-muted opacity-50">↑↓</span></template>
                                                    <template x-if="sortField === 'name'">
                                                        <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th>الوصف</th>

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
                                    @forelse($this->customerGroupsList as $group)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chk_child"
                                                    value="{{ $group->id }}"
                                                    x-model="selectedIds">
                                            </div>
                                        </th>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a class="edit-item-btn" href="javascript:void(0);" @click="$wire.editCustomerGroup({{ $group->id }}).then(() => showModal = true)"><i class="ri-pencil-fill align-bottom text-muted"></i></a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Delete">
                                                    <a class="remove-item-btn" href="javascript:void(0);" @click="$wire.confirmDelete({{ $group->id }}).then(() => showDeleteModal = true)">
                                                        <i class="ri-delete-bin-fill align-bottom text-muted"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="code"><span class="badge bg-light text-body fs-12">{{ $group->code ?? '-' }}</span></td>
                                        <td class="name">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs rounded-circle bg-primary-subtle text-primary d-flex justify-content-center align-items-center me-2 fw-semibold">
                                                    {{ mb_substr($group->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h5 class="fs-14 my-1 fw-medium">{{ $group->name }}</h5>
                                                    <span class="text-muted mb-0">{{ $group->name_en }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="description">
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $group->description }}">
                                                {{ $group->description ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="status">
                                            <div class="form-check form-switch form-switch-md mb-2" dir="ltr">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    wire:click="toggleStatus({{ $group->id }})"
                                                    {{ $group->status->value === 1 ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="created_at">{{ optional($group->created_at)->diffForHumans() }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="noresult">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">عذراً! لم يتم العثور على نتائج</h5>
                                                    <p class="text-muted mb-0">لم نعثر على أي مجموعات مطابقة لبحثك.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{$this->customerGroupsList->links('livewire::custom-pagination-links')}}

                            <div wire:ignore.self
                                class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                :class="{ 'show d-block': showModal }" :aria-hidden="!showModal"
                                x-show="showModal" x-transition.opacity>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-light p-3">
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                @if($form->customerGroup)
                                                <i class="ri-edit-line me-2 text-warning"></i> تعديل بيانات المجموعة
                                                @else
                                                <i class="ri-group-line me-2 text-success"></i> إضافة مجموعة جديدة
                                                @endif
                                            </h5>
                                            <button type="button" class="btn-close" aria-label="Close" @click="showModal = false; $wire.cancel()"></button>
                                        </div>
                                        <form wire:submit.prevent="submitCustomerGroup" autocomplete="off">
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    {{-- عرض رسائل الخطأ للحقول المخفية --}}
                                                    @if($errors->has('form.code'))
                                                    <div class="col-12">
                                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                            <i class="ri-error-warning-line me-2 align-middle"></i>
                                                            <strong>تنبيه:</strong> حدث خطأ في الكود التلقائي:
                                                            <ul class="mb-0 mt-1">
                                                                @error('form.code') <li>{{ $message }}</li> @enderror
                                                            </ul>
                                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    <div class="col-12">
                                                        <label for="form-name" class="form-label">اسم المجموعة (بالعربية) <span class="text-danger">*</span></label>
                                                        <input type="text" id="form-name" class="form-control @error('form.name') is-invalid @enderror"
                                                            wire:model.blur="form.name" placeholder="مثال: عملاء الجملة" />
                                                        @error('form.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-12">
                                                        <label for="form-name_en" class="form-label">اسم المجموعة (بالإنجليزية)</label>
                                                        <input type="text" id="form-name_en" class="form-control @error('form.name_en') is-invalid @enderror"
                                                            wire:model.blur="form.name_en" placeholder="مثال: Wholesale Customers" />
                                                        @error('form.name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-12">
                                                        <label for="form-code" class="form-label">كود المجموعة <small class="text-muted text-capitalize">(اختياري - سيتم توليده تلقائياً إن ترك فارغاً)</small></label>
                                                        <input type="text" id="form-code" class="form-control text-uppercase @error('form.code') is-invalid @enderror"
                                                            wire:model.blur="form.code" placeholder="مثال: VIP-01" dir="ltr" />
                                                        @error('form.code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-12">
                                                        <label for="form-status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                                        <select id="form-status" class="form-select @error('form.status') is-invalid @enderror" wire:model.blur="form.status">
                                                            <option value="1">مفعل</option>
                                                            <option value="0">غير مفعل</option>
                                                        </select>
                                                        @error('form.status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-12">
                                                        <label for="form-description" class="form-label">الوصف والملاحظات</label>
                                                        <textarea id="form-description" class="form-control @error('form.description') is-invalid @enderror"
                                                            wire:model="form.description" rows="3" placeholder="اكتب وصفاً مختصراً لهذه المجموعة..."></textarea>
                                                        @error('form.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer mt-3">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="button" class="btn btn-light" @click="showModal = false; $wire.cancel()">
                                                        <i class="ri-close-line me-1"></i> إلغاء
                                                    </button>
                                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                                        <span wire:loading.remove wire:target="submitCustomerGroup">
                                                            @if($form->customerGroup)
                                                            <i class="ri-save-line me-1"></i> تحديث البيانات
                                                            @else
                                                            <i class="ri-save-line me-1"></i> حفظ المجموعة
                                                            @endif
                                                        </span>
                                                        <span wire:loading wire:target="submitCustomerGroup">
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
                                                <h4 class="fs-semibold">هل أنت متأكد من حذف هذه المجموعة؟</h4>
                                                <p class="text-muted fs-14 mb-4 pt-1">لن يتم حذف العملاء المرتبطين بها، ولكن سيتم إزالة تصنيفهم من هذه المجموعة.</p>
                                                <div class="hstack gap-2 justify-content-center remove">
                                                    <button class="btn btn-light" @click="showDeleteModal = false">
                                                        <i class="ri-close-line me-1 align-middle"></i> إلغاء
                                                    </button>
                                                    <button class="btn btn-danger" wire:click="deleteGroup" @click="showDeleteModal = false">
                                                        <i class="ri-delete-bin-fill me-1"></i> نعم، احذف!
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @include('partials.backend.customer-groups.offcanvas')

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
                window.dispatchEvent(new CustomEvent('close-group-modal'));
            });
        });
    </script>
    @endpush
</div>