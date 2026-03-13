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
                                    placeholder="ابحث عن اسم المرحلة، الكود..."
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
                                            @click="if(confirm('هل أنت متأكد من حذف المراحل المحددة؟')) $wire.deleteMultiple()">
                                            <i class="ri-delete-bin-2-line"></i> (<span x-text="selectedIds.length"></span>)
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-info" @click="showOffcanvas = true"><i class="ri-filter-3-line align-bottom me-1"></i> تصفية</button>
                                    <button type="button" class="btn btn-success add-btn" @click="$wire.cancel(); showModal = true" id="create-btn"><i class="ri-add-line align-bottom me-1"></i> إضافة مرحلة</button>
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
            <div class="card" id="pipelineStagesList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-10">
                            <h5 class="card-title mb-0">إدارة مراحل تدفق المبيعات (Pipeline)</h5>
                            <p><small>عرض وتنسيق مراحل الصفقات ونسب احتمالية الفوز لكل مرحلة.</small></p>
                        </div>

                        <div class="col-sm-auto ms-auto">
                            <div class="d-flex align-items-center gap-2" wire:ignore>
                                <span class="text-muted">عرض: </span>
                                <select class="form-control mb-0" wire:model.live="perPage">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <div class="table-responsive table-card mb-3">
                            <table class="table align-middle table-nowrap mb-0" id="pipelineStageTable">
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
                                                <span>اسم المرحلة</span>
                                                <span class="fs-11 ms-1">
                                                    <template x-if="sortField === 'name'">
                                                        <span><span :class="sortDirection === 'asc' ? 'text-primary' : 'text-muted opacity-50'">↑</span><span :class="sortDirection === 'desc' ? 'text-primary' : 'text-muted opacity-50'">↓</span></span>
                                                    </template>
                                                </span>
                                            </div>
                                        </th>

                                        <th @click="sortBy('probability')" style="cursor: pointer; user-select: none;">
                                            <span>الاحتمالية</span>
                                        </th>

                                        <th>النوع</th>

                                        <th @click="sortBy('sort_order')" style="cursor: pointer; user-select: none;">
                                            <span>الترتيب</span>
                                        </th>

                                        <th @click="sortBy('status')" style="cursor: pointer; user-select: none;">
                                            <span>الحالة</span>
                                        </th>
                                        <th>انشئ في</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    @forelse($this->pipelineStagesList as $stage)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chk_child"
                                                    value="{{ $stage->id }}"
                                                    x-model="selectedIds">
                                            </div>
                                        </th>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item">
                                                    <a class="edit-item-btn" href="javascript:void(0);" @click="$wire.editStage({{ $stage->id }}).then(() => showModal = true)"><i class="ri-pencil-fill align-bottom text-muted"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a class="remove-item-btn" href="javascript:void(0);" @click="$wire.confirmDelete({{ $stage->id }}).then(() => showDeleteModal = true)">
                                                        <i class="ri-delete-bin-fill align-bottom text-muted"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="name">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xxs flex-shrink-0 me-2">
                                                    <span class="avatar-title rounded-circle fs-10" style="background-color: {{ $stage->color ?? '#405189' }}">
                                                        {{ $stage->sort_order }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="fs-14 my-1 fw-medium">{{ $stage->name }}</h5>
                                                    <span class="text-muted mb-0 fs-11">{{ $stage->name_en }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="probability">
                                            <span class="badge bg-info-subtle text-info fs-12">{{ number_format($stage->probability, 0) }}%</span>
                                        </td>
                                        <td class="type">
                                            @if($stage->is_won)
                                            <span class="badge bg-success">فوز (Won)</span>
                                            @elseif($stage->is_lost)
                                            <span class="badge bg-danger">خسارة (Lost)</span>
                                            @else
                                            <span class="badge bg-light text-body">تقدم</span>
                                            @endif
                                        </td>
                                        <td class="sort_order">{{ $stage->sort_order }}</td>
                                        <td class="status">
                                            <div class="form-check form-switch form-switch-md mb-2" dir="ltr">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    wire:click="toggleStatus({{ $stage->id }})"
                                                    {{ $stage->status->value === 1 ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="created_at">{{ optional($stage->created_at)->diffForHumans() }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="noresult">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">عذراً! لم يتم العثور على نتائج</h5>
                                                    <p class="text-muted mb-0">لم نعثر على أي مراحل مطابقة لبحثك.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{$this->pipelineStagesList->links('livewire::custom-pagination-links')}}

                            <div wire:ignore.self
                                class="modal fade"
                                id="showModal"
                                tabindex="-1"
                                :class="{ 'show d-block': showModal }"
                                :aria-hidden="!showModal"
                                x-show="showModal"
                                x-transition.opacity>
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-light p-3">
                                            <h5 class="modal-title">
                                                @if($form->pipelineStage)
                                                <i class="ri-edit-line me-2 text-warning"></i> تعديل المرحلة
                                                @else
                                                <i class="ri-add-box-line me-2 text-success"></i> إضافة مرحلة مبيعات جديدة
                                                @endif
                                            </h5>
                                            <button type="button" class="btn-close" aria-label="Close" @click="showModal = false; $wire.cancel()"></button>
                                        </div>
                                        <form wire:submit.prevent="submitStage" autocomplete="off">
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-lg-6">
                                                        <label class="form-label">اسم المرحلة (عربي) <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('form.name') is-invalid @enderror"
                                                            wire:model.blur="form.name" placeholder="مثال: تقديم العرض" />
                                                        @error('form.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="form-label">الاسم بالإنجليزية</label>
                                                        <input type="text" class="form-control"
                                                            wire:model.blur="form.name_en" placeholder="Example: Proposal" />
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <label class="form-label">احتمالية الفوز (%) <span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control @error('form.probability') is-invalid @enderror"
                                                            wire:model.blur="form.probability" placeholder="0-100" />
                                                        @error('form.probability') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <label class="form-label">كود المرحلة</label>
                                                        <input type="text" class="form-control text-uppercase"
                                                            wire:model.blur="form.code" placeholder="STG-01" />
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <label class="form-label">ترتيب العرض</label>
                                                        <input type="number" class="form-control" wire:model.blur="form.sort_order" />
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="form-label">اللون المميز</label>
                                                        <input type="color" class="form-control form-control-color w-100" wire:model.live="form.color" />
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                                        <select class="form-select" wire:model.blur="form.status">
                                                            <option value="1">مفعل</option>
                                                            <option value="0">غير مفعل</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="form-check form-check-inline mt-2">
                                                            <input class="form-check-input" type="checkbox" id="wonCheck" wire:model="form.is_won">
                                                            <label class="form-check-label text-success" for="wonCheck fw-semibold">تعتبر مرحلة فوز بالصفقة (Won)</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="form-check form-check-inline mt-2">
                                                            <input class="form-check-input" type="checkbox" id="lostCheck" wire:model="form.is_lost">
                                                            <label class="form-check-label text-danger" for="lostCheck fw-semibold">تعتبر مرحلة خسارة للصفقة (Lost)</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label">الوصف</label>
                                                        <textarea class="form-control" wire:model="form.description" rows="2" placeholder="ملاحظات حول المرحلة..."></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="button" class="btn btn-light" @click="showModal = false; $wire.cancel()">إلغاء</button>
                                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                                        <span wire:loading.remove wire:target="submitStage"><i class="ri-save-line me-1"></i> حفظ المرحلة</span>
                                                        <span wire:loading wire:target="submitStage"><span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...</span>
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
                                        <div class="modal-header"><button type="button" class="btn-close" @click="showDeleteModal = false"></button></div>
                                        <div class="modal-body p-5 text-center">
                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                                            <div class="mt-4 text-center">
                                                <h4 class="fs-semibold">حذف مرحلة التدفق؟</h4>
                                                <p class="text-muted">حذف هذه المرحلة قد يؤثر على الصفقات المرتبطة بها حالياً.</p>
                                                <div class="hstack gap-2 justify-content-center remove">
                                                    <button class="btn btn-light" @click="showDeleteModal = false">إلغاء</button>
                                                    <button class="btn btn-danger" wire:click="deleteStage" @click="showDeleteModal = false">نعم، احذف!</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('partials.backend.pipeline-stages.offcanvas')
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
            timerProgressBar: true
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', ({
                type,
                message
            }) => {
                Toast.fire({
                    icon: type,
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