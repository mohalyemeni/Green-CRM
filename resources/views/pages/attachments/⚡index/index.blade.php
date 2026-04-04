<div x-data="{
    sortField: @entangle('sortField'),
    sortDirection: @entangle('sortDirection'),
    showDeleteModal: @entangle('showDeleteModal'),
    sortBy(field) {
        $wire.sortBy(field);
    }
}"
    x-on:open-delete-modal.window="showDeleteModal = true"
    x-on:close-delete-modal.window="showDeleteModal = false">

    {{-- ============================= --}}
    {{-- شريط العنوان (Page Header)   --}}
    {{-- ============================= --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    <i class="ri-archive-2-line me-2 text-info"></i>
                    أرشيف المرفقات
                    <span class="fs-14 text-muted fw-normal ms-2">الفرص البيعية</span>
                </h4>
                <div class="page-title-right hstack gap-2">
                    <a href="{{ route('admin.opportunities.index') }}" class="btn btn-soft-success btn-sm">
                        <i class="ri-funds-line align-bottom me-1"></i> الفرص البيعية
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= --}}
    {{-- بطاقات الإحصائيات (Stats)    --}}
    {{-- ============================= --}}
    @php
    $stats = $this->stats;
    $totalSizeMB = $stats['total_size'] >= 1048576
    ? round($stats['total_size'] / 1048576, 1) . ' MB'
    : ($stats['total_size'] >= 1024 ? round($stats['total_size'] / 1024, 1) . ' KB' : $stats['total_size'] . ' B');
    @endphp
    <div class="row g-3 mb-3">
        {{-- إجمالي المرفقات --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fw-medium text-muted mb-0">إجمالي المرفقات</p>
                            <h2 class="mt-2 ff-secondary fw-semibold mb-0 text-info">
                                {{ number_format($stats['total']) }}
                            </h2>
                            <p class="mb-0 text-muted fs-12 mt-1">
                                <span>الحجم الكلي: <strong>{{ $totalSizeMB }}</strong></span>
                            </p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle text-info rounded-3 fs-26">
                                <i class="ri-attachment-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ملفات PDF --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fw-medium text-muted mb-0">ملفات PDF</p>
                            <h2 class="mt-2 ff-secondary fw-semibold mb-0 text-danger">
                                {{ number_format($stats['pdf_count']) }}
                            </h2>
                            <p class="mb-0 text-muted fs-12 mt-1">
                                @if($stats['total'])
                                {{ round(($stats['pdf_count'] / $stats['total']) * 100) }}% من الإجمالي
                                @else
                                لا توجد ملفات
                                @endif
                            </p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle text-danger rounded-3 fs-26">
                                <i class="ri-file-pdf-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- الصور --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fw-medium text-muted mb-0">ملفات الصور</p>
                            <h2 class="mt-2 ff-secondary fw-semibold mb-0 text-success">
                                {{ number_format($stats['img_count']) }}
                            </h2>
                            <p class="mb-0 text-muted fs-12 mt-1">
                                @if($stats['total'])
                                {{ round(($stats['img_count'] / $stats['total']) * 100) }}% من الإجمالي
                                @else
                                لا توجد ملفات
                                @endif
                            </p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle text-success rounded-3 fs-26">
                                <i class="ri-image-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ملفات أخرى --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fw-medium text-muted mb-0">ملفات أخرى</p>
                            <h2 class="mt-2 ff-secondary fw-semibold mb-0 text-warning">
                                {{ number_format($stats['total'] - $stats['pdf_count'] - $stats['img_count']) }}
                            </h2>
                            <p class="mb-0 text-muted fs-12 mt-1">Word، Excel، ضغط، وغيرها</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle text-warning rounded-3 fs-26">
                                <i class="ri-file-list-3-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= --}}
    {{-- شريط البحث والتصفية          --}}
    {{-- ============================= --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header border-0">
                    <div class="row g-3 align-items-center">
                        <div class="col-sm-6">
                            <div class="search-box position-relative" x-data="{ search: @entangle('search') }">
                                <input type="text"
                                    class="form-control search bg-light border-light"
                                    placeholder="ابحث بـ: اسم الملف، اسم العميل، رقم الهاتف، الفرصة، الوصف..."
                                    wire:model.lazy="search">
                                <i class="ri-search-line search-icon" wire:loading.remove wire:target="search"></i>
                                <div class="spinner-border spinner-border-sm search-icon text-primary"
                                    role="status" wire:loading wire:target="search"></div>
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
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAttachmentFilters">
                                    <i class="ri-filter-3-line align-bottom me-1"></i> تصفية متقدمة
                                    @if($filterType || $filterUploader || $filterOpportunity || $created_from || $created_to)
                                    <span class="badge bg-danger ms-1 rounded-pill">!</span>
                                    @endif
                                </button>
                                @if($filterType || $filterUploader || $filterOpportunity || $created_from || $created_to || $search)
                                <button type="button" class="btn btn-soft-secondary btn-sm" wire:click="resetFilters">
                                    <i class="ri-refresh-line align-bottom me-1"></i> إعادة ضبط
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= --}}
    {{-- الجدول الرئيسي               --}}
    {{-- ============================= --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0" id="attachmentsList">
                <div class="card-header border-0">
                    <div class="row g-3 align-items-center">
                        <div class="col-sm-8">
                            <h5 class="card-title mb-0">
                                <i class="ri-archive-2-line text-info me-2"></i>
                                أرشيف ملفات الفرص البيعية
                            </h5>
                            <p class="mb-0 mt-1">
                                <small class="text-muted">جميع المرفقات المرتبطة بالفرص البيعية المسجلة في النظام.</small>
                            </p>
                        </div>
                        <div class="col-sm-auto ms-auto">
                            <div class="d-flex align-items-center gap-2" wire:ignore>
                                <span class="text-muted fs-13">عرض:</span>
                                <select class="form-control form-control-sm mb-0" wire:model.live="perPage" style="width: 70px;">
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-0">
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-hover mb-0" id="attachmentsTable">
                            <thead class="table-light">
                                <tr>
                                    {{-- الإجراءات --}}
                                    <th class="text-center" style="width: 100px;">الإجراءات</th>

                                    {{-- اسم الملف --}}
                                    <th @click="sortBy('file_name')" style="cursor: pointer; user-select: none; min-width: 220px;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>اسم الملف</span>
                                            <span class="fs-11 ms-2">
                                                <template x-if="sortField !== 'file_name'"><span class="text-muted opacity-50">↑↓</span></template>
                                                <template x-if="sortField === 'file_name'">
                                                    <span>
                                                        <span :class="sortDirection === 'asc' ? 'text-info' : 'text-muted opacity-50'">↑</span>
                                                        <span :class="sortDirection === 'desc' ? 'text-info' : 'text-muted opacity-50'">↓</span>
                                                    </span>
                                                </template>
                                            </span>
                                        </div>
                                    </th>

                                    {{-- الفرصة المرتبطة --}}
                                    <th style="min-width: 180px;">الفرصة البيعية</th>

                                    {{-- العميل --}}
                                    <th style="min-width: 140px;">العميل</th>

                                    {{-- نوع الملف --}}
                                    <th @click="sortBy('file_type')" style="cursor: pointer; user-select: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>نوع الملف</span>
                                            <span class="fs-11 ms-2">
                                                <template x-if="sortField !== 'file_type'"><span class="text-muted opacity-50">↑↓</span></template>
                                                <template x-if="sortField === 'file_type'">
                                                    <span>
                                                        <span :class="sortDirection === 'asc' ? 'text-info' : 'text-muted opacity-50'">↑</span>
                                                        <span :class="sortDirection === 'desc' ? 'text-info' : 'text-muted opacity-50'">↓</span>
                                                    </span>
                                                </template>
                                            </span>
                                        </div>
                                    </th>

                                    {{-- الحجم --}}
                                    <th @click="sortBy('file_size')" style="cursor: pointer; user-select: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>الحجم</span>
                                            <span class="fs-11 ms-2">
                                                <template x-if="sortField !== 'file_size'"><span class="text-muted opacity-50">↑↓</span></template>
                                                <template x-if="sortField === 'file_size'">
                                                    <span>
                                                        <span :class="sortDirection === 'asc' ? 'text-info' : 'text-muted opacity-50'">↑</span>
                                                        <span :class="sortDirection === 'desc' ? 'text-info' : 'text-muted opacity-50'">↓</span>
                                                    </span>
                                                </template>
                                            </span>
                                        </div>
                                    </th>

                                    {{-- الوصف --}}
                                    <th style="min-width: 160px;">الوصف</th>

                                    {{-- رافع الملف --}}
                                    <th>رُفع بواسطة</th>

                                    {{-- تاريخ الرفع --}}
                                    <th @click="sortBy('created_at')" style="cursor: pointer; user-select: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>تاريخ الرفع</span>
                                            <span class="fs-11 ms-2">
                                                <template x-if="sortField !== 'created_at'"><span class="text-muted opacity-50">↑↓</span></template>
                                                <template x-if="sortField === 'created_at'">
                                                    <span>
                                                        <span :class="sortDirection === 'asc' ? 'text-info' : 'text-muted opacity-50'">↑</span>
                                                        <span :class="sortDirection === 'desc' ? 'text-info' : 'text-muted opacity-50'">↓</span>
                                                    </span>
                                                </template>
                                            </span>
                                        </div>
                                    </th>

                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($this->attachmentsList as $attachment)
                                @php
                                $opportunity = $attachment->attachmentable;
                                @endphp
                                <tr>
                                    {{-- الإجراءات --}}
                                    <td class="text-center">
                                        <div class="hstack justify-content-center">
                                            {{-- تحميل --}}
                                            <button type="button"
                                                wire:click="downloadAttachment({{ $attachment->id }})"
                                                class="btn btn-ghost-info btn-icon btn-sm"
                                                title="تحميل الملف"
                                                data-bs-toggle="tooltip">
                                                <i class="ri-download-2-line fs-15"></i>
                                            </button>

                                            {{-- عرض الفرصة --}}
                                            @if($opportunity)
                                            <a href="{{ route('admin.opportunities.show', $opportunity->id) }}"
                                                class="btn btn-ghost-success btn-icon btn-sm"
                                                title="عرض الفرصة"
                                                data-bs-toggle="tooltip">
                                                <i class="ri-eye-line fs-15"></i>
                                            </a>
                                            @endif

                                            {{-- حذف --}}
                                            <button type="button"
                                                class="btn btn-ghost-danger btn-icon btn-sm"
                                                wire:click="confirmDelete({{ $attachment->id }})"
                                                title="حذف المرفق"
                                                data-bs-toggle="tooltip">
                                                <i class="ri-delete-bin-line fs-15"></i>
                                            </button>
                                        </div>
                                    </td>
                                    {{-- اسم الملف مع أيقونة --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title rounded bg-light border fs-18">
                                                    <i class="{{ $attachment->file_icon }}"></i>
                                                </span>
                                            </div>
                                            <div style="min-width: 0;">
                                                <p class="mb-0 fw-medium fs-13 text-dark text-truncate" style="max-width: 200px;" title="{{ $attachment->file_name }}">
                                                    {{ $attachment->file_name }}
                                                </p>
                                                <small class="text-muted" dir="ltr">{{ $attachment->file_size_formatted }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- الفرصة المرتبطة --}}
                                    <td>
                                        @if($opportunity)
                                        <a href="{{ route('admin.opportunities.show', $opportunity->id) }}"
                                            class="text-dark fw-medium text-truncate d-block fs-13"
                                            style="max-width: 170px;"
                                            title="{{ $opportunity->title }}">
                                            <i class="ri-funds-line text-success me-1"></i>
                                            {{ $opportunity->title }}
                                        </a>
                                        <small class="text-muted" dir="ltr">{{ $opportunity->opportunity_number ?? '' }}</small>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- العميل --}}
                                    <td>
                                        @if($attachment->customer)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xxs">
                                                <div class="avatar-title rounded-circle bg-primary-subtle text-primary fw-bold fs-11">
                                                    {{ mb_substr($attachment->customer->name, 0, 1) }}
                                                </div>
                                            </div>
                                            <span class="fs-13 fw-medium text-dark">{{ $attachment->customer->name }}</span>
                                        </div>
                                        @else
                                        <span class="text-muted fst-italic fs-13">—</span>
                                        @endif
                                    </td>

                                    {{-- نوع الملف --}}
                                    <td>
                                        @php
                                        $type = strtolower($attachment->file_type ?? '');
                                        if (str_contains($type, 'pdf')) {
                                        $typeBadge = ['label' => 'PDF', 'color' => 'danger'];
                                        } elseif (str_contains($type, 'image')) {
                                        $typeBadge = ['label' => 'صورة', 'color' => 'success'];
                                        } elseif (str_contains($type, 'word') || str_contains($type, 'doc')) {
                                        $typeBadge = ['label' => 'Word', 'color' => 'primary'];
                                        } elseif (str_contains($type, 'excel') || str_contains($type, 'sheet')) {
                                        $typeBadge = ['label' => 'Excel', 'color' => 'success'];
                                        } elseif (str_contains($type, 'zip') || str_contains($type, 'rar')) {
                                        $typeBadge = ['label' => 'ضغط', 'color' => 'warning'];
                                        } else {
                                        $typeBadge = ['label' => $attachment->file_type ? explode('/', $attachment->file_type)[1] ?? $attachment->file_type : 'ملف', 'color' => 'secondary'];
                                        }
                                        @endphp
                                        <span class="badge bg-{{ $typeBadge['color'] }}-subtle text-{{ $typeBadge['color'] }} border border-{{ $typeBadge['color'] }}-subtle">
                                            {{ $typeBadge['label'] }}
                                        </span>
                                    </td>

                                    {{-- الحجم --}}
                                    <td>
                                        <span class="fw-medium text-dark fs-13">{{ $attachment->file_size_formatted }}</span>
                                    </td>

                                    {{-- الوصف --}}
                                    <td>
                                        @if($attachment->description)
                                        <span class="text-muted fs-13 text-truncate d-block" style="max-width: 150px;" title="{{ $attachment->description }}">
                                            {{ $attachment->description }}
                                        </span>
                                        @else
                                        <span class="text-muted fst-italic fs-13">لا يوجد وصف</span>
                                        @endif
                                    </td>

                                    {{-- رافع الملف --}}
                                    <td>
                                        @if($attachment->uploader)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xxs">
                                                <div class="avatar-title rounded-circle bg-info-subtle text-info fw-bold fs-11">
                                                    {{ mb_substr($attachment->uploader->first_name ?? 'U', 0, 1) }}
                                                </div>
                                            </div>
                                            <span class="fs-13">{{ $attachment->uploader->full_name ?? $attachment->uploader->first_name }}</span>
                                        </div>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- تاريخ الرفع --}}
                                    <td>
                                        <div>
                                            <span class="fs-13 fw-medium">{{ $attachment->created_at->format('Y/m/d') }}</span>
                                            <br>
                                            <small class="text-muted">{{ $attachment->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="text-center py-5">
                                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                                colors="primary:#121331,secondary:#08a88a"
                                                style="width:80px;height:80px">
                                            </lord-icon>
                                            <h5 class="mt-3 text-muted">لا توجد مرفقات</h5>
                                            <p class="text-muted fs-13 mb-0">
                                                @if($search || $filterType || $filterUploader || $filterOpportunity)
                                                لم يتم العثور على مرفقات مطابقة للتصفية الحالية.
                                                <br>
                                                <button type="button" class="btn btn-link p-0 fs-13" wire:click="resetFilters">
                                                    إعادة ضبط التصفية
                                                </button>
                                                @else
                                                لم يتم رفع أي مرفقات للفرص البيعية بعد.
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $this->attachmentsList->links('livewire::custom-pagination-links') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= --}}
    {{-- Modal: تأكيد الحذف            --}}
    {{-- ============================= --}}
    <div wire:ignore.self
        id="deleteAttachmentModal"
        class="modal fade zoomIn"
        tabindex="-1"
        x-show="showDeleteModal"
        :class="{ 'show d-block': showDeleteModal }"
        :aria-hidden="!showDeleteModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" aria-label="Close"
                        @click="showDeleteModal = false"></button>
                </div>
                <div class="modal-body p-5 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                        colors="primary:#405189,secondary:#f06548"
                        style="width:90px;height:90px">
                    </lord-icon>
                    <div class="mt-4 text-center">
                        <h4 class="fs-semibold">هل أنت متأكد من حذف هذا المرفق؟</h4>
                        <p class="text-muted fs-14 mb-4 pt-1">
                            سيتم حذف الملف نهائياً ولا يمكن التراجع عن هذا الإجراء.
                        </p>
                        <div class="hstack gap-2 justify-content-center">
                            <button class="btn btn-light" @click="showDeleteModal = false">
                                <i class="ri-close-line me-1 align-middle"></i> إلغاء
                            </button>
                            <button class="btn btn-danger"
                                wire:click="deleteAttachment"
                                wire:loading.attr="disabled"
                                @click="showDeleteModal = false">
                                <span wire:loading.remove wire:target="deleteAttachment">
                                    <i class="ri-delete-bin-fill me-1"></i> نعم، احذف!
                                </span>
                                <span wire:loading wire:target="deleteAttachment">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    جاري الحذف...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"
        x-show="showDeleteModal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1040;">
    </div>

    {{-- ============================= --}}
    {{-- Offcanvas: التصفية المتقدمة  --}}
    {{-- ============================= --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAttachmentFilters" aria-labelledby="offcanvasFiltersLabel"
        x-on:filters-reset.window="() => {}">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasFiltersLabel">
                <i class="ri-filter-3-line me-2 text-info"></i>
                التصفية المتقدمة للمرفقات
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="row g-3">

                {{-- تصفية حسب نوع الملف --}}
                <div class="col-12">
                    <label class="form-label fw-medium">نوع الملف</label>
                    <select class="form-select" wire:model.live="filterType">
                        <option value="">— جميع الأنواع —</option>
                        <option value="pdf">PDF</option>
                        <option value="image">صور (Image)</option>
                        <option value="word">Word</option>
                        <option value="excel">Excel / Sheet</option>
                        <option value="zip">ملفات مضغوطة (ZIP/RAR)</option>
                    </select>
                </div>

                {{-- تصفية حسب الفرصة --}}
                <div class="col-12">
                    <label class="form-label fw-medium">الفرصة البيعية</label>
                    <select class="form-select" wire:model.live="filterOpportunity">
                        <option value="">— جميع الفرص —</option>
                        @foreach($this->opportunities as $opp)
                        <option value="{{ $opp->id }}">{{ $opp->title }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- تصفية حسب رافع الملف --}}
                <div class="col-12">
                    <label class="form-label fw-medium">رُفع بواسطة</label>
                    <select class="form-select" wire:model.live="filterUploader">
                        <option value="">— جميع المستخدمين —</option>
                        @foreach($this->uploaders as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- تاريخ الرفع من --}}
                <div class="col-12">
                    <label class="form-label fw-medium">تاريخ الرفع من</label>
                    <input type="date" class="form-control" wire:model.live="created_from">
                </div>

                {{-- تاريخ الرفع إلى --}}
                <div class="col-12">
                    <label class="form-label fw-medium">تاريخ الرفع إلى</label>
                    <input type="date" class="form-control" wire:model.live="created_to">
                </div>

                <hr class="my-1">

                {{-- أزرار التحكم --}}
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-info w-100" wire:click="applyFilters" data-bs-dismiss="offcanvas">
                            <i class="ri-filter-3-line me-1"></i> تطبيق التصفية
                        </button>
                        <button type="button" class="btn btn-soft-secondary" wire:click="resetFilters">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', ({
                type,
                message
            }) => {
                const iconMap = {
                    success: 'success',
                    info: 'info',
                    warning: 'warning',
                    error: 'error'
                };
                Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                }).fire({
                    icon: iconMap[type] ?? 'info',
                    title: message
                });
            });

            Livewire.on('close-offcanvas', () => {
                var el = document.getElementById('offcanvasAttachmentFilters');
                if (el) {
                    var instance = bootstrap.Offcanvas.getInstance(el);
                    if (instance) instance.hide();
                }
            });

            // تفعيل tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el, {
                    trigger: 'hover'
                });
            });
        });
    </script>
    @endpush

</div>