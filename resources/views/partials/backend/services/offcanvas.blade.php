<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" wire:ignore.self
    :class="{ 'show': showOffcanvas }"
    :style="showOffcanvas ? 'visibility: visible;' : 'visibility: hidden;'">

    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">تصفية مجموعات الخدمات</h5>
        <button type="button" class="btn-close text-reset" @click="$wire.resetFilters(); showOffcanvas = false" aria-label="Close"></button>
    </div>

    <form wire:submit.prevent="applyFilters" class="d-flex flex-column flex-grow-1 overflow-hidden">
        <div class="offcanvas-body">

            {{-- فلتر تاريخ الإنشاء --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">تاريخ الإنشاء</label>
                <div class="row g-2 align-items-center">
                    <div class="col-lg">
                        <input type="date" class="form-control" wire:model="created_from" id="sg_created_from" placeholder="من تاريخ">
                    </div>
                    <div class="col-lg-auto">إلى</div>
                    <div class="col-lg">
                        <input type="date" class="form-control" wire:model="created_to" id="sg_created_to" placeholder="إلى تاريخ">
                    </div>
                </div>
            </div>

            {{-- فلتر الحالة --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">الحالة</label>
                <div class="row g-2">
                    @foreach(\App\Enums\ActiveStatus::cases() as $status)
                    <div class="col-lg-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                wire:model="selectedStatuses"
                                id="sg_status_{{ $status->value }}"
                                value="{{ $status->value }}">
                            <label class="form-check-label text-{{ $status->color() }}" for="sg_status_{{ $status->value }}">
                                {{ $status->label() }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>



            {{-- فلتر المجموعة الأب --}}
            {{-- Parent Groups Filter (مكتبة Choices) باستخدام الدالة المحسوبة --}}
            <div class="mb-4" wire:ignore x-data="{
            choice: null,
            init() {
                this.choice = new Choices(this.$refs.parentSelect, {
                    searchEnabled: true,
                    removeItemButton: true,
                    shouldSort: false,
                    placeholderValue: 'ابحث عن المجموعة...',
                    itemSelectText: ''
                });

                window.addEventListener('filters-reset', () => {
                    this.choice.removeActiveItems();
                });

                this.$refs.parentSelect.addEventListener('change', (e) => {
                    let selectedValues = Array.from(e.target.selectedOptions, option => option.value);
                    $wire.set('selectedParents', selectedValues, false);
                    });
                }
            }">
                <label for="parent-select" class="form-label text-muted text-uppercase fw-semibold mb-3">تتبع لمجموعة</label>
                <select x-ref="parentSelect" class="form-control" id="parent-select" multiple>
                    <option value="">مجموعة رئيسية...</option>
                    @foreach($this->parentGroups as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
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