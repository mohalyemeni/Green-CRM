<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasServices" aria-labelledby="offcanvasServicesLabel" wire:ignore.self
    :class="{ 'show': showOffcanvas }"
    :style="showOffcanvas ? 'visibility: visible;' : 'visibility: hidden;'">

    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title" id="offcanvasServicesLabel">
            <i class="ri-filter-3-line me-2 text-primary"></i>تصفية الخدمات
        </h5>
        <button type="button" class="btn-close text-reset" @click="$wire.resetFilters(); showOffcanvas = false" aria-label="Close"></button>
    </div>

    <form wire:submit.prevent="applyFilters" class="d-flex flex-column flex-grow-1 overflow-hidden">
        <div class="offcanvas-body">

            {{-- فلتر تاريخ الإنشاء --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">
                    <i class="ri-calendar-line me-1"></i> تاريخ الإنشاء
                </label>
                <div class="row g-2 align-items-center">
                    <div class="col-lg">
                        <input type="date" class="form-control" wire:model="created_from" id="srv_created_from" placeholder="من تاريخ">
                    </div>
                    <div class="col-lg-auto">إلى</div>
                    <div class="col-lg">
                        <input type="date" class="form-control" wire:model="created_to" id="srv_created_to" placeholder="إلى تاريخ">
                    </div>
                </div>
            </div>

            {{-- فلتر الحالة --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">
                    <i class="ri-toggle-line me-1"></i> الحالة
                </label>
                <div class="row g-2">
                    @foreach(\App\Enums\ActiveStatus::cases() as $status)
                    <div class="col-lg-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                wire:model="selectedStatuses"
                                id="srv_status_{{ $status->value }}"
                                value="{{ $status->value }}">
                            <label class="form-check-label text-{{ $status->color() }}" for="srv_status_{{ $status->value }}">
                                {{ $status->label() }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- فلتر نوع الخصم --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">
                    <i class="ri-percent-line me-1"></i> نوع الخصم
                </label>
                <div class="row g-2">
                    @foreach(\App\Enums\DiscountType::cases() as $dtype)
                    <div class="col-lg-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                wire:model="selectedDiscounts"
                                id="srv_discount_{{ $dtype->value }}"
                                value="{{ $dtype->value }}">
                            <label class="form-check-label text-{{ $dtype->color() }}" for="srv_discount_{{ $dtype->value }}">
                                {{ $dtype->symbol() }} {{ $dtype->label() }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- فلتر المجموعات (Choices.js) --}}
            <div class="mb-4" wire:ignore x-data="{
                choice: null,
                init() {
                    this.choice = new Choices(this.$refs.groupSelect, {
                        searchEnabled: true,
                        removeItemButton: true,
                        shouldSort: false,
                        placeholderValue: 'ابحث عن المجموعة...',
                        itemSelectText: ''
                    });

                    window.addEventListener('filters-reset', () => {
                        this.choice.removeActiveItems();
                    });

                    this.$refs.groupSelect.addEventListener('change', (e) => {
                        let selectedValues = Array.from(e.target.selectedOptions, option => option.value);
                        $wire.set('selectedGroups', selectedValues, false);
                    });
                }
            }">
                <label for="group-select" class="form-label text-muted text-uppercase fw-semibold mb-3">
                    <i class="ri-stack-line me-1"></i> مجموعة الخدمة
                </label>
                <select x-ref="groupSelect" class="form-control" id="group-select" multiple>
                    @foreach($this->activeServiceGroups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="offcanvas-footer border-top p-3 text-center hstack gap-2 mt-auto">
            <button type="button" class="btn btn-light w-100" wire:click="resetFilters">
                <i class="ri-refresh-line me-1"></i> مسح الفلتر
            </button>
            <button type="submit" class="btn btn-success w-100">
                <i class="ri-filter-3-line me-1"></i> تطبيق الفلتر
            </button>
        </div>
    </form>
</div>