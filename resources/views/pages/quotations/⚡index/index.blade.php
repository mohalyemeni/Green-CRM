<div x-data="{
    selectedIds: @entangle('selectedIds'),
    sortField: @entangle('sortField'),
    sortDirection: @entangle('sortDirection'),
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
                                    placeholder="ابحث عن رقم العرض، عنوان العرض، أو اسم العميل..."
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
                                            @click="if(confirm('هل أنت متأكد من حذف العروض المحددة؟')) $wire.deleteMultiple()">
                                            <i class="ri-delete-bin-2-line"></i> (<span x-text="selectedIds.length"></span>)
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-info" @click="showOffcanvas = true">
                                        <i class="ri-filter-3-line align-bottom me-1"></i> تصفية
                                    </button>
                                    <a href="{{ route('admin.quotations.create') }}" class="btn btn-success add-btn">
                                        <i class="ri-add-line align-bottom me-1"></i> إنشاء عرض سعر جديد
                                    </a>
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
            <div class="card" id="quotationsList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-10">
                            <h5 class="card-title mb-0">إدارة عروض الأسعار</h5>
                            <p><small>عرض وإدارة جميع عروض الأسعار المقدمة للعملاء.</small></p>
                        </div>
                        <div class="col-sm-auto ms-auto">
                            <div class="d-flex align-items-center gap-2" wire:ignore>
                                <span class="text-muted">عرض: </span>
                                <select class="form-control mb-0" wire:model.live="perPage">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
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
                            <table class="table align-middle table-nowrap mb-0" id="quotationsTable">
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
                                        <th @click="sortBy('code')" style="cursor: pointer; user-select: none;">رقم العرض</th>
                                        <th @click="sortBy('title')" style="cursor: pointer; user-select: none;">العنوان</th>
                                        <th @click="sortBy('customer_name')" style="cursor: pointer; user-select: none;">العميل</th>
                                        <th @click="sortBy('issue_date')" style="cursor: pointer; user-select: none;">التاريخ</th>
                                        <th @click="sortBy('total')" style="cursor: pointer; user-select: none;">الإجمالي</th>
                                        <th @click="sortBy('status')" style="cursor: pointer; user-select: none;">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    @forelse($this->quotationsList as $quotation)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chk_child"
                                                    value="{{ $quotation->id }}"
                                                    x-model="selectedIds">
                                            </div>
                                        </th>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="عرض">
                                                    <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="text-primary d-inline-block">
                                                        <i class="ri-eye-fill fs-16"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="تعديل">
                                                    <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="edit-item-btn text-muted">
                                                        <i class="ri-pencil-fill align-bottom"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="حذف">
                                                    <a class="remove-item-btn text-muted" href="javascript:void(0);"
                                                        @click="$wire.confirmDelete({{ $quotation->id }}).then(() => showDeleteModal = true)">
                                                        <i class="ri-delete-bin-fill align-bottom"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td><span class="badge bg-light text-body fs-12 fw-medium border"><i class="ri-file-list-3-line text-muted me-1"></i>{{ $quotation->code }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <h5 class="fs-14 mb-0 fw-medium">
                                                    <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="text-reset">{{ $quotation->title }}</a>
                                                </h5>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs bg-light rounded p-1 me-2"><img src="https://ui-avatars.com/api/?name={{ urlencode($quotation->customer_name) }}&color=7F9CF5&background=EBF4FF" alt="" class="img-fluid rounded-circle"></div>
                                                <div>
                                                    <h6 class="mb-0">{{ $quotation->customer_name }}</h6>
                                                    @if($quotation->customer_phone)<p class="text-muted mb-0 fs-12">{{ $quotation->customer_phone }}</p>@endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ \Carbon\Carbon::parse($quotation->issue_date)->format('Y-m-d') }}</span>
                                            <span class="text-muted fs-11">ينتهي: {{ \Carbon\Carbon::parse($quotation->expiry_date)->format('Y-m-d') }}</span>
                                        </td>
                                        <td class="fw-semibold text-dark">{{ number_format((float) $quotation->total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $quotation->status->color() }}-subtle text-{{ $quotation->status->color() }} fs-11" style="padding: 5px 10px;">
                                                <i class="{{ $quotation->status->icon() }} me-1 align-bottom"></i>
                                                {{ $quotation->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="noresult">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                                        colors="primary:#121331,secondary:#08a88a"
                                                        style="width:75px;height:75px">
                                                    </lord-icon>
                                                    <h5 class="mt-2">عذراً! لم يتم العثور على نتائج</h5>
                                                    <p class="text-muted mb-0">لم نعثر على أي عرض سعر مطابق لبحثك.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{ $this->quotationsList->links('livewire::custom-pagination-links') }}
                            
                            {{-- Modal: تأكيد الحذف --}}
                            <div wire:ignore.self
                                id="deleteQuotationModal" class="modal fade zoomIn" tabindex="-1"
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
                                                <h4 class="fs-semibold">هل أنت متأكد من حذف عرض السعر؟</h4>
                                                <p class="text-muted fs-14 mb-4 pt-1">سيتم نقل عرض السعر لسلة المهملات.</p>
                                                <div class="hstack gap-2 justify-content-center remove">
                                                    <button class="btn btn-light" @click="showDeleteModal = false">
                                                        <i class="ri-close-line me-1 align-middle"></i> إلغاء
                                                    </button>
                                                    <button class="btn btn-danger"
                                                        wire:click="deleteQuotation"
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
                            @include('partials.backend.quotations.offcanvas')

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Backdrops --}}
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
            Livewire.on('notify', ({ type, message }) => {
                const iconMap = { success: 'success', info: 'info', warning: 'warning', error: 'error' };
                Toast.fire({
                    icon: iconMap[type] ?? 'info',
                    title: message
                });
            });
        });

        @if(session()->has('notify'))
        document.addEventListener('DOMContentLoaded', function () {
            const notifyInfo = @json(session('notify'));
            const iconMap = { success: 'success', info: 'info', warning: 'warning', error: 'error' };
            Toast.fire({
                icon: iconMap[notifyInfo.type] ?? 'info',
                title: notifyInfo.message
            });
        });
        @endif
    </script>
    @endpush
</div>