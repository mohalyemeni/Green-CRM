<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFilters" aria-labelledby="offcanvasFiltersLabel" wire:ignore.self>
    <div class="offcanvas-header bg-light border-bottom p-3">
        <h5 class="offcanvas-title d-flex align-items-center" id="offcanvasFiltersLabel">
            <i class="ri-filter-3-line text-primary me-2 fs-20"></i>
            <span>تصفية الفرص البيعية</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <div class="p-4" style="padding-bottom: 100px !important;">

            {{-- تصفية بالفترة الزمنية --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold fs-12 mb-2">الفترة الزمنية</label>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fs-13 mb-1">من تاريخ</label>
                        <input type="date" class="form-control form-control-sm" wire:model="created_from">
                    </div>
                    <div class="col-6">
                        <label class="form-label fs-13 mb-1">إلى تاريخ</label>
                        <input type="date" class="form-control form-control-sm" wire:model="created_to">
                    </div>
                </div>
            </div>

            <hr class="border-dashed mb-4">

            {{-- تصفية بالمصدر --}}
            <div class="mb-4" wire:ignore x-data="{
                choice: null,
                init() {
                    this.choice = new Choices(this.$refs.sourceSelect, {
                        searchEnabled: true,
                        removeItemButton: true,
                        shouldSort: false,
                        placeholderValue: 'ابحث عن المصدر...',
                        itemSelectText: ''
                    });
                    window.addEventListener('filters-reset', () => { this.choice.removeActiveItems(); });
                    this.$refs.sourceSelect.addEventListener('change', (e) => {
                        let selectedValues = Array.from(e.target.selectedOptions, option => option.value);
                        $wire.set('selectedSources', selectedValues, false);
                    });
                }
            }">
                <label for="opp-source-select" class="form-label text-muted text-uppercase fw-semibold fs-12 mb-2">
                    <i class="ri-share-forward-line me-1"></i> المصدر (Source)
                </label>
                <select x-ref="sourceSelect" class="form-control" id="opp-source-select" multiple>
                    @foreach($this->sources as $source)
                        <option value="{{ $source->id }}">{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- تصفية بالمسؤول --}}
            <div class="mb-4" wire:ignore x-data="{
                choice: null,
                init() {
                    this.choice = new Choices(this.$refs.assigneeSelect, {
                        searchEnabled: true,
                        removeItemButton: true,
                        shouldSort: false,
                        placeholderValue: 'ابحث عن المسؤول...',
                        itemSelectText: ''
                    });
                    window.addEventListener('filters-reset', () => { this.choice.removeActiveItems(); });
                    this.$refs.assigneeSelect.addEventListener('change', (e) => {
                        let selectedValues = Array.from(e.target.selectedOptions, option => option.value);
                        $wire.set('selectedAssignees', selectedValues, false);
                    });
                }
            }">
                <label for="opp-assignee-select" class="form-label text-muted text-uppercase fw-semibold fs-12 mb-2">
                    <i class="ri-user-received-line me-1"></i> المسؤول (Assignee)
                </label>
                <select x-ref="assigneeSelect" class="form-control" id="opp-assignee-select" multiple>
                    @foreach($this->users as $user)
                        <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- تصفية بالمرحلة --}}
            <div class="mb-4" wire:ignore x-data="{
                choice: null,
                init() {
                    this.choice = new Choices(this.$refs.stageSelect, {
                        searchEnabled: true,
                        removeItemButton: true,
                        shouldSort: false,
                        placeholderValue: 'ابحث عن المرحلة...',
                        itemSelectText: ''
                    });
                    window.addEventListener('filters-reset', () => { this.choice.removeActiveItems(); });
                    this.$refs.stageSelect.addEventListener('change', (e) => {
                        let selectedValues = Array.from(e.target.selectedOptions, option => option.value);
                        $wire.set('selectedStages', selectedValues, false);
                    });
                }
            }">
                <label for="opp-stage-select" class="form-label text-muted text-uppercase fw-semibold fs-12 mb-2">
                    <i class="ri-kanban-line me-1"></i> مرحلة المبيعات (Stage)
                </label>
                <select x-ref="stageSelect" class="form-control" id="opp-stage-select" multiple>
                    @foreach($this->stages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- تصفية بالأولوية --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold fs-12 mb-2">الأولوية</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($this->priorities as $priority)
                        <div>
                            <input type="checkbox" class="btn-check" value="{{ $priority['value'] }}" wire:model="selectedPriorities" id="opp_priority_{{ $priority['value'] }}">
                            <label class="btn btn-sm btn-outline-{{ $priority['color'] }}" for="opp_priority_{{ $priority['value'] }}">
                                {{ $priority['label'] }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <hr class="border-dashed mb-4">

            {{-- فرص بدون تفاعل --}}
            <div class="alert alert-danger border-2 border-danger-subtle bg-danger-subtle p-3 mb-0" role="alert">
                <div class="form-check form-switch form-check-danger mb-2 d-flex align-items-center px-0">
                    <input class="form-check-input ms-0 me-2" style="width: 35px; height: 18px; cursor: pointer;" type="checkbox" wire:model="withoutComments48h" id="noOppComments">
                    <label class="form-check-label fw-bold fs-14 mb-0" style="cursor: pointer;" for="noOppComments">فرص بدون تفاعل</label>
                </div>
                <p class="mb-0 fs-12 text-danger mt-1">يُظهر الفرص التي لم تسجل لها أي نشاطات أو تعليقات خلال الـ <strong class="fw-bold fs-13">48 ساعة الماضية</strong>.</p>
            </div>
        </div>
    </div>

    {{-- الأزرار السفلية --}}
    <div class="offcanvas-footer border-top p-3 text-center bg-white" style="position: absolute; bottom: 0; width: 100%; z-index: 9;">
        <div class="row g-2">
            <div class="col-6">
                <button type="button" class="btn btn-outline-danger w-100" wire:click="resetFilters">
                    <i class="ri-refresh-line me-1 align-bottom"></i> مسح الكل
                </button>
            </div>
            <div class="col-6">
                <button type="button" class="btn btn-primary w-100" wire:click="applyFilters" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="applyFilters">
                        <i class="ri-check-double-line me-1 align-bottom"></i> تطبيق الفلاتر
                    </span>
                    <span wire:loading wire:target="applyFilters">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span> جاري التطبيق...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
