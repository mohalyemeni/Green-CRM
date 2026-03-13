<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" wire:ignore.self
    :class="{ 'show': showOffcanvas }"
    :style="showOffcanvas ? 'visibility: visible;' : 'visibility: hidden;'">
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">تصفية حالات العملاء</h5>
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
                    <div class="col-lg-auto">إلى</div>
                    <div class="col-lg">
                        <input type="date" class="form-control" wire:model="created_to" id="created_to" placeholder="إلى تاريخ">
                    </div>
                </div>
            </div>

            {{-- Status Filter (Active/Inactive) --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">حالة التفعيل</label>
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

            {{-- Lead Status Logic Filters --}}
            <div class="mb-4">
                <label class="form-label text-muted text-uppercase fw-semibold mb-3">نوع الحالة (المنطق)</label>
                <div class="row g-3">
                    <div class="col-lg-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="is_default" id="filter_is_default" value="1">
                            <label class="form-check-label" for="filter_is_default">
                                عرض الحالات الافتراضية فقط
                            </label>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="is_closed" id="filter_is_closed" value="1">
                            <label class="form-check-label" for="filter_is_closed">
                                عرض حالات الإغلاق فقط
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- تنبيه مساعدة --}}
            <div class="alert alert-info border-0 shadow-none mb-0">
                <p class="mb-0 fs-13">يمكنك تصفية الحالات بناءً على تاريخ إضافتها أو خصائصها الفنية كحالة افتراضية أو حالة إغلاق للملف.</p>
            </div>

        </div>

        <div class="offcanvas-footer border-top p-3 text-center hstack gap-2 mt-auto">
            <button type="button" class="btn btn-light w-100" wire:click="resetFilters">مسح الفلتر</button>
            <button type="submit" class="btn btn-success w-100">تطبيق الفلتر</button>
        </div>
    </form>
</div>