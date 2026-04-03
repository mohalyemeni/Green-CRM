<div wire:ignore.self class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasQuotationsFilter"
    x-show="showOffcanvas" :class="{ 'show': showOffcanvas }"
    style="visibility: visible;"
    @click.outside="showOffcanvas = false"
    style="width: 400px; z-index: 1045;">

    <div class="offcanvas-header bg-light border-bottom">
        <h5 class="offcanvas-title" id="offcanvasFilterLabel">
            <i class="ri-filter-3-line align-middle me-1"></i> تصفية متقدمة
        </h5>
        <button type="button" class="btn-close" @click="showOffcanvas = false" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <form wire:submit.prevent="applyFilters">
            <div class="p-4" style="max-height: calc(100vh - 130px); overflow-y: auto;">

                {{-- تاريخ الإصدار (من - إلى) --}}
                <div class="mb-4">
                    <label class="form-label text-muted text-uppercase fw-semibold mb-3">تاريخ الإصدار</label>
                    <div class="row g-2 align-items-center">
                        <div class="col-lg">
                            <input type="date" class="form-control" wire:model="created_from" placeholder="من تاريخ">
                        </div>
                        <div class="col-lg-auto">إلى</div>
                        <div class="col-lg">
                            <input type="date" class="form-control" wire:model="created_to" placeholder="إلى تاريخ">
                        </div>
                    </div>
                </div>

                <hr class="border-light my-4">

                {{-- الحالات المتعددة --}}
                <div class="mb-4" wire:ignore x-data="{
                    choice: null,
                    init() {
                        // تهيئة مكتبة Choices على عنصر الـ Select
                        this.choice = new Choices(this.$refs.statusSelect, {
                            searchEnabled: true,
                            removeItemButton: true,
                            shouldSort: false,
                            placeholderValue: 'اختر حالات العرض...',
                            itemSelectText: ''
                        });

                        // الاستماع لحدث إعادة تعيين الفلاتر
                        window.addEventListener('filters-reset', () => {
                            this.choice.removeActiveItems();
                        });

                        // تحديث قيمة Livewire عند تغيير الاختيارات
                        this.$refs.statusSelect.addEventListener('change', (e) => {
                            let selectedValues = Array.from(e.target.selectedOptions, option => option.value);
                            $wire.set('selectedStatuses', selectedValues, false);
                        });
                    }
                }">
                    <label for="status-select" class="form-label text-muted text-uppercase fw-semibold mb-3">
                        <i class="ri-checkbox-circle-line me-1"></i> حالات العرض
                    </label>

                    <select x-ref="statusSelect" class="form-control" id="status-select" multiple>
                        @foreach(App\Enums\QuotationStatus::cases() as $status)
                        <option value="{{ $status->value }}">
                            {{ $status->label() }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="offcanvas-footer border-top p-3 text-center position-absolute bottom-0 w-100 bg-white shadow-lg">
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-light w-100" wire:click="resetFilters">
                            <i class="ri-refresh-line align-bottom me-1"></i> مسح الفلاتر
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-search-line align-bottom me-1"></i> تطبيق التصفية
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>