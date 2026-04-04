<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFilters" aria-labelledby="offcanvasFiltersLabel" wire:ignore.self>
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="offcanvasFiltersLabel">الفلاتر المتقدمة</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        
        <div class="mb-3">
            <label class="form-label">المصدر</label>
            @foreach($sources as $source)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $source->id }}" wire:model="selectedSources" id="source_{{ $source->id }}">
                    <label class="form-check-label" for="source_{{ $source->id }}">
                        {{ $source->name }}
                    </label>
                </div>
            @endforeach
        </div>

        <div class="mb-3">
            <label class="form-label">المسؤول</label>
            @foreach($users as $user)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $user->id }}" wire:model="selectedAssignees" id="user_{{ $user->id }}">
                    <label class="form-check-label" for="user_{{ $user->id }}">
                        {{ $user->name }}
                    </label>
                </div>
            @endforeach
        </div>

        <div class="mb-3">
            <label class="form-label">الأولوية</label>
            @foreach($priorities as $priority)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $priority->value }}" wire:model="selectedPriorities" id="priority_{{ $priority->value }}">
                    <label class="form-check-label" for="priority_{{ $priority->value }}">
                        <span class="badge bg-{{ $priority->color() }}">{{ $priority->label() }}</span>
                    </label>
                </div>
            @endforeach
        </div>

        <hr>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" wire:model="withoutComments48h" id="noComments">
                <label class="form-check-label fw-bold text-danger" for="noComments">عملاء بدون تفاعل لأكثر من 48 ساعة</label>
            </div>
            <div class="form-text">يعرض العملاء الذين لم يتم إضافة أي تعليق/نشاط على ملفهم خلال الـ 48 ساعة الماضية</div>
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-primary" wire:click="applyFilters">تطبيق الفلاتر</button>
            <button class="btn btn-outline-secondary" wire:click="resetFilters">مسح الكل</button>
        </div>

    </div>
</div>
