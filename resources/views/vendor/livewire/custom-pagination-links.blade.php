@php
if (! isset($scrollTo)) {
$scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
? <<<JS
    (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '' ;
    @endphp

    <div class="mt-2">
    @if ($paginator->hasPages())
    <nav class="d-flex flex-column flex-md-row justify-content-between align-items-center bg-light p-3 rounded-3 shadow-sm">

        {{-- قسم معلومات البيانات (يسار) --}}
        <div class="text-muted mb-3 mb-md-0">
            <span class="d-flex align-items-center">
                <i class="ri-list-check-2 me-2 text-primary fs-18"></i>
                <span>
                    {{ __('عرض') }}
                    <span class="fw-bold text-dark">{{ $paginator->firstItem() }}</span>
                    {{ __('إلى') }}
                    <span class="fw-bold text-dark">{{ $paginator->lastItem() }}</span>
                    {{ __('من إجمالي') }}
                    <span class="badge bg-primary-subtle text-primary px-2 py-1 mx-1">{{ $paginator->total() }}</span>
                    {{ __('سجل') }}
                </span>
            </span>
        </div>

        {{-- أزرار التنقل (يمين) --}}
        <div class="pagination-wrap">
            <ul class="pagination pagination-rounded mb-0 gap-2">

                {{-- زر السابق --}}
                @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link d-flex align-items-center px-3">
                        <i class="ri-arrow-right-s-line me-1"></i> {{ __('السابق') }}
                    </span>
                </li>
                @else
                <li class="page-item">
                    <button type="button"
                        class="page-link d-flex align-items-center px-3 shadow-none"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled">
                        <i class="ri-arrow-right-s-line me-1"></i> {{ __('السابق') }}
                    </button>
                </li>
                @endif

                {{-- زر التالي --}}
                @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button type="button"
                        class="page-link d-flex align-items-center px-3 shadow-none"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled">
                        {{ __('التالي') }} <i class="ri-arrow-left-s-line ms-1"></i>
                    </button>
                </li>
                @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link d-flex align-items-center px-3">
                        {{ __('التالي') }} <i class="ri-arrow-left-s-line ms-1"></i>
                    </span>
                </li>
                @endif

            </ul>
        </div>
    </nav>
    @endif
    </div>

    {{-- تنسيقات CSS إضافية لضمان الاحترافية --}}
    <style>
        .pagination-rounded .page-link {
            border-radius: 8px !important;
            border: 1px solid #e9ebec;
            color: #495057;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .pagination-rounded .page-item:not(.disabled) .page-link:hover {
            background-color: var(--vz-primary);
            color: white;
            border-color: var(--vz-primary);
            transform: translateY(-1px);
        }

        .pagination-rounded .page-item.disabled .page-link {
            background-color: #f3f6f9;
            color: #adb5bd;
        }

        [dir="rtl"] .ri-arrow-right-s-line {
            transform: rotate(0deg);
        }

        [dir="rtl"] .ri-arrow-left-s-line {
            transform: rotate(0deg);
        }
    </style>