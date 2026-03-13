<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" wire:ignore.self
    :class="{ 'show': showOffcanvas }"
    :style="showOffcanvas ? 'visibility: visible;' : 'visibility: hidden;'">
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">تصفية الدول</h5>
        <button type="button" class="btn-close text-reset" @click="$wire.resetFilters(); showOffcanvas = false" aria-label="Close"></button>
    </div>

    <form wire:submit.prevent="applyFilters" class="d-flex flex-column flex-grow-1 overflow-hidden">
        <div class="offcanvas-body">

            {{-- Date Range Filter --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">تاريخ الإنشاء</label>
                <div class="row g-2 align-items-center">
                    <div class="col-lg">
                        <input type="date" class="form-control" wire:model="created_from" id="created_from" placeholder="من تاريخ">
                    </div>
                    <div class="col-lg-auto">
                        إلى
                    </div>
                    <div class="col-lg">
                        <input type="date" class="form-control" wire:model="created_to" id="created_to" placeholder="إلى تاريخ">
                    </div>
                </div>
            </div>

            {{-- Status Filter --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">الحالة</label>
                <div class="row g-2">
                    @foreach(\App\Enums\ActiveStatus::cases() as $status)
                    <div class="col-lg-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="selectedStatuses" id="status_{{ $status->value }}" value="{{ $status->value }}">
                            <label class="form-check-label text-{{ $status->color() }}" for="status_{{ $status->value }}">
                                {{ $status->label() }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Phone Code Filter (باستخدام مكتبة Choices) --}}
            <div class="mb-4" wire:ignore x-data="{
                choice: null,
                init() {
                    this.choice = new Choices(this.$refs.select, {
                        searchEnabled: true,
                        removeItemButton: true,
                        shouldSort: false,
                        placeholderValue: 'ابحث عن مفتاح اتصال...',
                        itemSelectText: ''
                    });

                    window.addEventListener('filters-reset', () => {
                        this.choice.removeActiveItems();
                    });

                    this.$refs.select.addEventListener('change', (e) => {
                        let selectedValues = Array.from(e.target.selectedOptions, option => option.value);
                        $wire.set('selectedPhoneCodes', selectedValues, false);
                    });
                }
            }">
                <label for="phonecode-select" class="form-label text-muted text-uppercase fw-semibold mb-3">مفتاح الاتصال الدولي</label>
                <select x-ref="select" class="form-control" id="phonecode-select" multiple dir="ltr">
                    <option value="">اختر مفتاح الاتصال...</option>
                    {{-- جلب مفاتيح الاتصال المميزة (بدون تكرار) الموجودة في قاعدة البيانات --}}
                    @foreach(\App\Models\Country::whereNotNull('phone_code')->distinct()->pluck('phone_code') as $code)
                    <option value="{{ $code }}">{{ $code }}</option>
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