<div x-data="{
    selectedIds: @entangle('selectedIds'),
    sortField: @entangle('sortField'),
    sortDirection: @entangle('sortDirection'),
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
    x-on:open-delete-modal.window="showDeleteModal = true"
    x-on:close-delete-modal.window="showDeleteModal = false">

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
                                    placeholder="ابحث عن اسم، جوال، بريد، شركة..."
                                    wire:model.lazy="search">
                                <i class="ri-search-line search-icon" wire:loading.remove wire:target="search"></i>
                                <div class="spinner-border spinner-border-sm search-icon text-primary"
                                    role="status" wire:loading wire:target="search"></div>
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
                                    <button type="button" class="btn btn-soft-danger"
                                        @click="if(confirm('هل أنت متأكد من حذف العملاء المحددين؟')) $wire.deleteMultiple()">
                                        <i class="ri-delete-bin-2-line"></i> (<span x-text="selectedIds.length"></span>)
                                    </button>
                                </div>
                                <button type="button" class="btn btn-info" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFilters">
                                    <i class="ri-filter-3-line align-bottom me-1"></i> تصفية
                                </button>
                                <a href="{{ route('admin.leads.create') }}" class="btn btn-success">
                                    <i class="ri-add-line align-bottom me-1"></i> إضافة عميل محتمل
                                </a>
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
            <div class="card" id="leadsList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-10">
                            <h5 class="card-title mb-0">إدارة العملاء المحتملين</h5>
                            <p><small>عرض وإدارة جميع العملاء المحتملين المسجلين في النظام.</small></p>
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
                    <div class="table-responsive table-card mb-3">
                        <table class="table align-middle table-nowrap mb-0" id="leadsTable">
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

                                    <th @click="sortBy('first_name')" style="cursor: pointer; user-select: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>اسم العميل</span>
                                            <span class="fs-11 ms-1">
                                                <template x-if="sortField !== 'first_name'"><span class="text-muted opacity-50">↑↓</span></template>
                                                <template x-if="sortField === 'first_name'">
                                                    <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                </template>
                                            </span>
                                        </div>
                                    </th>

                                    <th>المصدر</th>
                                    <th>المسؤول</th>

                                    <th @click="sortBy('lead_status_id')" style="cursor: pointer; user-select: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>الحالة</span>
                                            <span class="fs-11 ms-1">
                                                <template x-if="sortField !== 'lead_status_id'"><span class="text-muted opacity-50">↑↓</span></template>
                                                <template x-if="sortField === 'lead_status_id'">
                                                    <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                </template>
                                            </span>
                                        </div>
                                    </th>

                                    <th @click="sortBy('priority')" style="cursor: pointer; user-select: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>الأولوية</span>
                                            <span class="fs-11 ms-1">
                                                <template x-if="sortField !== 'priority'"><span class="text-muted opacity-50">↑↓</span></template>
                                                <template x-if="sortField === 'priority'">
                                                    <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                </template>
                                            </span>
                                        </div>
                                    </th>

                                    <th @click="sortBy('created_at')" style="cursor: pointer; user-select: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>أنشئ في</span>
                                            <span class="fs-11 ms-1">
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
                                @forelse($this->leadsList as $lead)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="chk_child"
                                                value="{{ $lead->id }}"
                                                x-model="selectedIds">
                                        </div>
                                    </th>
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-placement="top" title="عرض الملف">
                                                <a href="{{ route('admin.leads.show', $lead->id) }}" class="text-info">
                                                    <i class="ri-eye-fill align-bottom"></i>
                                                </a>
                                            </li>
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-placement="top" title="تعديل">
                                                <a href="{{ route('admin.leads.edit', $lead->id) }}" class="edit-item-btn">
                                                    <i class="ri-pencil-fill align-bottom text-muted"></i>
                                                </a>
                                            </li>
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-placement="top" title="حذف">
                                                <a class="remove-item-btn" href="javascript:void(0);"
                                                    @click="$wire.confirmDelete({{ $lead->id }}).then(() => showDeleteModal = true)">
                                                    <i class="ri-delete-bin-fill align-bottom text-muted"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs rounded-circle bg-primary-subtle text-primary d-flex justify-content-center align-items-center me-2 fw-bold">
                                                {{ mb_substr($lead->first_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h5 class="fs-14 my-1 fw-medium">
                                                    <a href="{{ route('admin.leads.show', $lead->id) }}" class="text-dark">
                                                        {{ $lead->first_name }} {{ $lead->last_name }}
                                                    </a>
                                                </h5>
                                                <small class="text-muted" dir="ltr">{{ $lead->mobile }}</small>
                                                @if($lead->company_name)
                                                    <br><small class="text-muted"><i class="ri-building-line"></i> {{ $lead->company_name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if($lead->source)
                                            <span class="badge bg-primary-subtle text-primary">{{ $lead->source->name }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>{{ $lead->assignee?->full_name ?? '—' }}</td>

                                    <td>
                                        @if($lead->status)
                                            <span class="badge" style="background-color: {{ $lead->status->color ?? '#6c757d' }}; color: #fff;">
                                                {{ $lead->status->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @php $priority = \App\Enums\PriorityLevel::tryFrom($lead->priority); @endphp
                                        @if($priority)
                                            <span class="badge bg-{{ $priority->color() }}-subtle text-{{ $priority->color() }}">
                                                {{ $priority->label() }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>{{ optional($lead->created_at)->diffForHumans() }}</td>
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
                                                <p class="text-muted mb-0">لم نعثر على أي عميل محتمل مطابق لبحثك.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        {{ $this->leadsList->links('livewire::custom-pagination-links') }}

                        {{-- Modal: تأكيد الحذف --}}
                        <div wire:ignore.self
                            id="deleteLeadModal" class="modal fade zoomIn" tabindex="-1"
                            x-show="showDeleteModal"
                            :class="{ 'show d-block': showDeleteModal }" :aria-hidden="!showDeleteModal">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="btn-close" aria-label="Close"
                                            @click="showDeleteModal = false"></button>
                                    </div>
                                    <div class="modal-body p-5 text-center">
                                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                            colors="primary:#405189,secondary:#f06548"
                                            style="width:90px;height:90px">
                                        </lord-icon>
                                        <div class="mt-4 text-center">
                                            <h4 class="fs-semibold">هل أنت متأكد من حذف هذا العميل؟</h4>
                                            <p class="text-muted fs-14 mb-4 pt-1">سيتم نقل العميل لسلة المهملات.</p>
                                            <div class="hstack gap-2 justify-content-center remove">
                                                <button class="btn btn-light" @click="showDeleteModal = false">
                                                    <i class="ri-close-line me-1 align-middle"></i> إلغاء
                                                </button>
                                                <button class="btn btn-danger"
                                                    wire:click="deleteLead"
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
                        @include('partials.backend.leads.offcanvas')

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Backdrops --}}
    <div class="modal-backdrop fade show" x-show="showDeleteModal" style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"></div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', ({ type, message }) => {
                const iconMap = { success: 'success', info: 'info', warning: 'warning', error: 'error' };
                Swal.mixin({
                    toast: true, position: 'top-end', showConfirmButton: false,
                    timer: 4000, timerProgressBar: true,
                }).fire({ icon: iconMap[type] ?? 'info', title: message });
            });
            Livewire.on('close-offcanvas', () => {
                var offcanvasElement = document.getElementById('offcanvasFilters');
                if (offcanvasElement) {
                    var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (offcanvasInstance) offcanvasInstance.hide();
                }
            });
        });
    </script>
    @endpush

</div>
