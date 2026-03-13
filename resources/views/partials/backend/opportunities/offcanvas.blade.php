<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" wire:ignore.self
    :class="{ 'show': showOffcanvas }"
    :style="showOffcanvas ? 'visibility: visible;' : 'visibility: hidden;'">
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">تصفية الفرص البيعية</h5>
        <button type="button" class="btn-close text-reset" @click="$wire.resetFilters(); showOffcanvas = false" aria-label="Close"></button>
    </div>

    <form wire:submit.prevent="applyFilters" class="d-flex flex-column flex-grow-1 overflow-hidden">
        <div class="offcanvas-body">

            {{-- Date Range Filter --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">تاريخ الإضافة</label>
                <div class="row g-2 align-items-center">
                    <div class="col-lg">
                        <input type="date" class="form-control" wire:model="created_from" id="created_from" placeholder="من تاريخ">
                    </div>
                    <div class="col-lg-auto">إلى</div>
                    <div class="col-lg">
                        <input type="date" class="form-control" wire:model="created_to" id="created_to" placeholder="إلى تاريخ">
                    </div>
                </div>
            </div>

            {{-- Priority Filter (باستخدام Choices.js) --}}
            <div class="mb-4" wire:ignore x-data="{
                choice: null,
                init() {
                    this.choice = new Choices(this.$refs.prioritySelect, {
                        searchEnabled: false,
                        removeItemButton: true,
                        placeholderValue: 'اختر مستوى الأولوية...',
                        itemSelectText: ''
                    });

                    window.addEventListener('filters-reset', () => {
                        this.choice.removeActiveItems();
                    });

                    this.$refs.prioritySelect.addEventListener('change', (e) => {
                        let selectedValues = Array.from(e.target.selectedOptions, option => option.value);
                        $wire.set('selectedPriorities', selectedValues, false);
                    });
                }
            }">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">الأولوية (Priority)</label>
                <select x-ref="prioritySelect" class="form-control" multiple>
                    <option value="urgent">عاجل جداً</option>
                    <option value="high">عالية</option>
                    <option value="medium">متوسطة</option>
                    <option value="low">منخفضة</option>
                </select>
            </div>

            {{-- Pipeline Stage Filter (باستخدام Choices.js) --}}
            <div class="mb-4" wire:ignore x-data="{
                choice: null,
                init() {
                    this.choice = new Choices(this.$refs.stageSelect, {
                        searchEnabled: true,
                        removeItemButton: true,
                        placeholderValue: 'اختر مراحل المبيعات...',
                        itemSelectText: ''
                    });

                    window.addEventListener('filters-reset', () => {
                        this.choice.removeActiveItems();
                    });

                    this.$refs.stageSelect.addEventListener('change', (e) => {
                        let selectedValues = Array.from(e.target.selectedOptions, option => option.value);
                        $wire.set('selectedStages', selectedValues, false);
                    });
                }
            }">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">مرحلة المبيعات (Stage)</label>
                <select x-ref="stageSelect" class="form-control" multiple>
                    @foreach(\App\Models\PipelineStage::orderBy('sort_order')->get() as $stage)
                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Opportunity Source Filter (باستخدام Choices.js) --}}
            <div class="mb-4" wire:ignore x-data="{
                choice: null,
                init() {
                    this.choice = new Choices(this.$refs.sourceSelect, {
                        searchEnabled: true,
                        removeItemButton: true,
                        placeholderValue: 'اختر مصادر الفرص...',
                        itemSelectText: ''
                    });

                    window.addEventListener('filters-reset', () => {
                        this.choice.removeActiveItems();
                    });

                    this.$refs.sourceSelect.addEventListener('change', (e) => {
                        let selectedValues = Array.from(e.target.selectedOptions, option => option.value);
                        $wire.set('selectedSources', selectedValues, false);
                    });
                }
            }">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">المصدر (Source)</label>
                <select x-ref="sourceSelect" class="form-control" multiple>
                    @foreach(\App\Models\OpportunitySource::all() as $source)
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