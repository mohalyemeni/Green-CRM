<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" wire:ignore.self
    :class="{ 'show': showOffcanvas }"
    :style="showOffcanvas ? 'visibility: visible;' : 'visibility: hidden;'">
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">تصفية العملاء المحتملين</h5>
        <button type="button" class="btn-close text-reset" @click="$wire.resetFilters(); showOffcanvas = false" aria-label="Close"></button>
    </div>

    <form wire:submit.prevent="applyFilters" class="d-flex flex-column flex-grow-1 overflow-hidden">
        <div class="offcanvas-body">

            {{-- Date Range Filter --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">تاريخ الإضافة</label>
                <div class="row g-2 align-items-center">
                    <div class="col-lg">
                        <input type="date" class="form-control" wire:model="created_from" id="created_from">
                    </div>
                    <div class="col-lg-auto">إلى</div>
                    <div class="col-lg">
                        <input type="date" class="form-control" wire:model="created_to" id="created_to">
                    </div>
                </div>
            </div>

            {{-- Priority Filter (Multiple Selection) --}}
            <div class="mb-4" wire:ignore x-data="{
                choice: null,
                init() {
                    this.choice = new Choices(this.$refs.prioritySelect, {
                        searchEnabled: false,
                        removeItemButton: true,
                        placeholderValue: 'اختر مستويات الأولوية...',
                        itemSelectText: ''
                    });
                    window.addEventListener('filters-reset', () => this.choice.removeActiveItems());
                    this.$refs.prioritySelect.addEventListener('change', (e) => {
                        let values = Array.from(e.target.selectedOptions, option => option.value);
                        $wire.set('selectedPriorities', values, false);
                    });
                }
            }">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">الأولوية</label>
                <select x-ref="prioritySelect" class="form-control" multiple>
                    <option value="1">عالية</option>
                    <option value="2">متوسطة</option>
                    <option value="3">منخفضة</option>
                </select>
            </div>

            {{-- Status Filter (Multiple Selection) --}}
            <div class="mb-4" wire:ignore x-data="{
                choice: null,
                init() {
                    this.choice = new Choices(this.$refs.statusSelect, {
                        searchEnabled: true,
                        removeItemButton: true,
                        placeholderValue: 'اختر الحالات...',
                        itemSelectText: ''
                    });
                    window.addEventListener('filters-reset', () => this.choice.removeActiveItems());
                    this.$refs.statusSelect.addEventListener('change', (e) => {
                        let values = Array.from(e.target.selectedOptions, option => option.value);
                        $wire.set('selectedStatuses', values, false);
                    });
                }
            }">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">الحالة</label>
                <select x-ref="statusSelect" class="form-control" multiple>
                    @foreach(\App\Models\LeadStatus::all() as $status)
                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Source Filter (Multiple Selection) --}}
            <div class="mb-4" wire:ignore x-data="{
                choice: null,
                init() {
                    this.choice = new Choices(this.$refs.sourceSelect, {
                        searchEnabled: true,
                        removeItemButton: true,
                        placeholderValue: 'اختر المصادر...',
                        itemSelectText: ''
                    });
                    window.addEventListener('filters-reset', () => this.choice.removeActiveItems());
                    this.$refs.sourceSelect.addEventListener('change', (e) => {
                        let values = Array.from(e.target.selectedOptions, option => option.value);
                        $wire.set('selectedSources', values, false);
                    });
                }
            }">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">المصدر</label>
                <select x-ref="sourceSelect" class="form-control" multiple>
                    @foreach(\App\Models\LeadSource::all() as $source)
                    <option value="{{ $source->id }}">{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="offcanvas-footer border-top p-3 text-center hstack gap-2 mt-auto">
            <button type="button" class="btn btn-light w-100" wire:click="resetFilters">مسح الفلتر</button>
            <button type="submit" class="btn btn-success w-100">تطبيق الفلتر</button>
        </div>
    </form>
</div>