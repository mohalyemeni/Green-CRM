<div>
    {{-- شريط العنوان --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    <i class="ri-funds-line me-2 text-success"></i>
                    {{ $opportunity->title }}
                    <span class="fs-14 text-muted fw-normal ms-2" dir="ltr">{{ $opportunity->opportunity_number }}</span>
                </h4>
                <div class="page-title-right hstack gap-2">
                    <a href="{{ route('admin.opportunities.edit', $opportunity->id) }}" class="btn btn-soft-warning">
                        <i class="ri-pencil-fill align-bottom me-1"></i> تعديل
                    </a>
                    <a href="{{ route('admin.opportunities.index') }}" class="btn btn-soft-secondary">
                        <i class="ri-arrow-right-line align-bottom me-1"></i> القائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- =============================== --}}
        {{-- العمود الأيسر: بيانات الفرصة   --}}
        {{-- =============================== --}}
        <div class="col-xl-4 col-lg-5">

            {{-- بطاقة الهوية --}}
            <div class="card">
                <div class="card-body text-center pt-4">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-success-subtle text-success rounded-circle fs-1 fw-bold">
                            {{ mb_substr($opportunity->title, 0, 1) }}
                        </div>
                    </div>
                    <h5 class="fs-16 mb-1">{{ $opportunity->title }}</h5>
                    @if($opportunity->customer)
                    <p class="text-muted mb-2">
                        <i class="ri-user-line me-1"></i>
                        {{ $opportunity->customer->name }}
                    </p>
                    @endif

                    {{-- الأولوية والمرحلة والإغلاق --}}
                    <div class="d-flex justify-content-center gap-2 flex-wrap mb-3">
                        @if($opportunity->stage)
                        <span class="badge fs-12" style="background-color: {{ $opportunity->stage->color ?? '#6c757d' }}">
                            {{ $opportunity->stage->name }}
                        </span>
                        @endif
                        @php
                        $priorityColors = ['low' => 'info', 'medium' => 'primary', 'high' => 'warning', 'urgent' => 'danger'];
                        $priorityLabels = ['low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'urgent' => 'عاجلة'];
                        $pc = $priorityColors[$opportunity->priority] ?? 'secondary';
                        $pl = $priorityLabels[$opportunity->priority] ?? '—';
                        @endphp
                        <span class="badge bg-{{ $pc }}-subtle text-{{ $pc }} border border-{{ $pc }} fs-12">{{ $pl }}</span>
                        @if($opportunity->closed_at)
                        <span class="badge bg-danger fs-12"><i class="ri-lock-2-line me-1"></i>مغلقة</span>
                        @endif
                    </div>
                </div>

                <div class="card-body border-top pt-3">
                    <ul class="list-unstyled mb-0 vstack gap-3">
                        <li class="d-flex align-items-center">
                            <i class="ri-hashtag text-primary fs-16 me-2 flex-shrink-0"></i>
                            <div>
                                <span class="text-muted fs-11">الرقم المرجعي</span>
                                <p class="mb-0 fw-medium" dir="ltr">{{ $opportunity->opportunity_number }}</p>
                            </div>
                        </li>
                        @if($opportunity->customer)
                        <li class="d-flex align-items-center">
                            <i class="ri-user-line text-primary fs-16 me-2 flex-shrink-0"></i>
                            <div>
                                <span class="text-muted fs-11">العميل</span>
                                <p class="mb-0 fw-medium">{{ $opportunity->customer->name }}</p>
                            </div>
                        </li>
                        @endif
                        <li class="d-flex align-items-center">
                            <i class="ri-share-forward-line text-primary fs-16 me-2 flex-shrink-0"></i>
                            <div>
                                <span class="text-muted fs-11">المصدر</span>
                                <p class="mb-0 fw-medium">{{ $opportunity->source?->name ?? '—' }}</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="ri-user-received-line text-primary fs-16 me-2 flex-shrink-0"></i>
                            <div>
                                <span class="text-muted fs-11">المسؤول</span>
                                <p class="mb-0 fw-medium">{{ $opportunity->assignee?->full_name ?? 'غير معين' }}</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="ri-money-dollar-circle-line text-success fs-16 me-2 flex-shrink-0"></i>
                            <div>
                                <span class="text-muted fs-11">الإيراد المتوقع</span>
                                <p class="mb-0 fw-medium text-success">{{ number_format($opportunity->expected_revenue, 2) }}</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="ri-percent-line text-primary fs-16 me-2 flex-shrink-0"></i>
                            <div>
                                <span class="text-muted fs-11">الاحتمالية</span>
                                <p class="mb-0 fw-medium">{{ $opportunity->probability }}%</p>
                            </div>
                        </li>
                        @if($opportunity->expected_close_date)
                        <li class="d-flex align-items-center">
                            <i class="ri-calendar-line text-primary fs-16 me-2 flex-shrink-0"></i>
                            <div>
                                <span class="text-muted fs-11">تاريخ الإغلاق المتوقع</span>
                                <p class="mb-0 fw-medium">{{ $opportunity->expected_close_date->format('Y/m/d') }}</p>
                            </div>
                        </li>
                        @endif
                        @if($opportunity->lostReason)
                        <li class="d-flex align-items-center">
                            <i class="ri-close-circle-line text-danger fs-16 me-2 flex-shrink-0"></i>
                            <div>
                                <span class="text-muted fs-11">سبب الخسارة</span>
                                <p class="mb-0 fw-medium text-danger">{{ $opportunity->lostReason->name }}</p>
                            </div>
                        </li>
                        @endif
                        <li class="d-flex align-items-center">
                            <i class="ri-calendar-line text-primary fs-16 me-2 flex-shrink-0"></i>
                            <div>
                                <span class="text-muted fs-11">تاريخ الإضافة</span>
                                <p class="mb-0 fw-medium">{{ $opportunity->created_at->format('Y/m/d') }}</p>
                            </div>
                        </li>
                        @if($opportunity->closed_at)
                        <li class="d-flex align-items-center">
                            <i class="ri-lock-2-line text-danger fs-16 me-2 flex-shrink-0"></i>
                            <div>
                                <span class="text-muted fs-11">تاريخ الإغلاق الفعلي</span>
                                <p class="mb-0 fw-medium text-danger">{{ $opportunity->closed_at->format('Y/m/d H:i') }}</p>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- سجل النشاطات --}}
            <div class="card mt-3">
                <div class="card-header border-bottom d-flex align-items-center">
                    <i class="ri-pulse-line text-warning me-2"></i>
                    <h6 class="mb-0">سجل النشاطات</h6>
                </div>
                <div class="card-body p-0">
                    <div class="p-3">
                        @forelse($this->activities as $activity)
                        <div class="d-flex align-items-start mb-3">
                            <div class="avatar-xxs rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0 me-2 mt-1">
                                <i class="ri-edit-line fs-12"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 fs-13">
                                    <span class="fw-medium">{{ $activity->user?->full_name ?? 'النظام' }}</span>
                                    — {{ $activity->description }}
                                </p>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-muted py-3 mb-0 fs-13">لا توجد نشاطات مسجلة</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- المرفقات --}}
            <div class="card mt-3">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ri-attachment-2 text-info me-2"></i>
                        <h6 class="mb-0">المرفقات</h6>
                    </div>
                    <span class="badge bg-info-subtle text-info">{{ $this->attachments->count() }}</span>
                </div>
                <div class="card-body">
                    {{-- صندوق رفع مرفق --}}
                    @auth
                    <form wire:submit="uploadAttachment" class="mb-3">
                        <div class="mb-2">
                            <input type="file" class="form-control form-control-sm @error('attachmentFile') is-invalid @enderror"
                                wire:model="attachmentFile"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                            @error('attachmentFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm"
                                wire:model="attachmentDesc"
                                placeholder="وصف المرفق (اختياري)">
                        </div>
                        <button type="submit" class="btn btn-info btn-sm w-100" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="uploadAttachment">
                                <i class="ri-upload-2-line me-1"></i> رفع الملف
                            </span>
                            <span wire:loading wire:target="uploadAttachment">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> جاري الرفع...
                            </span>
                        </button>
                    </form>
                    @endauth

                    {{-- قائمة المرفقات --}}
                    @forelse($this->attachments as $attachment)
                    <div class="d-flex align-items-center border rounded p-2 mb-2 overflow-hidden" style="min-width: 0;">

                        <div class="flex-shrink-0 me-2">
                            <i class="{{ $attachment->file_icon }} fs-20 text-primary"></i>
                        </div>

                        <div class="flex-grow-1" style="min-width: 0;">
                            <p class="mb-0 fs-13 fw-medium text-truncate text-dark" title="{{ $attachment->file_name }}">
                                {{ $attachment->file_name }}
                            </p>
                            <small class="text-muted d-block text-truncate">
                                {{ $attachment->file_size_formatted }}
                                @if($attachment->description)
                                <span class="text-secondary">· {{ $attachment->description }}</span>
                                @endif
                            </small>
                        </div>

                        <div class="d-flex gap-1 ms-2 flex-shrink-0">
                            <button type="button"
                                wire:click="downloadAttachment({{ $attachment->id }})"
                                class="btn btn-ghost-info btn-icon btn-sm" title="تحميل">
                                <i class="ri-download-line fs-14"></i>
                            </button>
                            @if($attachment->created_by === auth()->id())
                            <button type="button"
                                class="btn btn-ghost-danger btn-icon btn-sm"
                                wire:click="deleteAttachment({{ $attachment->id }})"
                                wire:confirm="هل أنت متأكد من حذف هذا المرفق؟"
                                title="حذف">
                                <i class="ri-delete-bin-line fs-14"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 border border-dashed rounded">
                        <i class="ri-file-line fs-24 d-block mb-1"></i>
                        <span class="fs-13">لا توجد مرفقات مرتبطة</span>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- =============================== --}}
        {{-- العمود الأيمن: التعليقات        --}}
        {{-- =============================== --}}
        <div class="col-xl-8 col-lg-7">

            {{-- صندوق إضافة تعليق --}}
            @auth
            <div class="card mb-3">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center">
                        <span class="bg-success-subtle text-success p-2 rounded-circle me-2">
                            <i class="ri-message-2-line fs-18"></i>
                        </span>
                        <h6 class="text-uppercase fw-bold mb-0">إضافة تعليق / نشاط جديد</h6>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit="addComment">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">نوع التعليق</label>
                                <select class="form-select" wire:model="commentType">
                                    @foreach($this->types as $type)
                                    @if($type !== \App\Enums\CommentType::CLOSED && $type !== \App\Enums\CommentType::SYSTEM)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-medium">المحتوى <span class="text-danger">*</span></label>
                                <textarea
                                    class="form-control @error('commentBody') is-invalid @enderror"
                                    rows="2"
                                    wire:model="commentBody"
                                    placeholder="سجّل ملاحظتك، مكالمتك، أو تفاصيل الاجتماع..."></textarea>
                                @error('commentBody') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="addComment">
                                    <i class="ri-send-plane-line align-bottom me-1"></i> نشر التعليق
                                </span>
                                <span wire:loading wire:target="addComment">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span> جاري النشر...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endauth

            {{-- الخط الزمني للتعليقات (Timeline) --}}
            <div class="card">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ri-history-line text-primary me-2 fs-18"></i>
                        <h6 class="mb-0">سجل المتابعة والتعليقات</h6>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">{{ $this->comments->count() }} تعليق</span>
                </div>
                <div class="card-body">
                    @forelse($this->comments as $comment)
                    @php
                    $cType = \App\Enums\CommentType::tryFrom($comment->type);
                    $isClosed = $cType === \App\Enums\CommentType::CLOSED;
                    $isSystem = $cType === \App\Enums\CommentType::SYSTEM;
                    @endphp
                    <div class="d-flex mb-4 @if($isClosed) p-3 rounded border border-danger bg-danger-subtle @endif">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <div class="avatar-title rounded-circle bg-{{ $cType ? $cType->color() : 'secondary' }}-subtle text-{{ $cType ? $cType->color() : 'secondary' }} fw-bold">
                                    {{ mb_substr($comment->user?->full_name ?? 'S', 0, 1) }}
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <h6 class="mb-0 fs-14">{{ $comment->user?->full_name ?? 'النظام' }}</h6>
                                    @if($cType)
                                    <span class="badge bg-{{ $cType->color() }}-subtle text-{{ $cType->color() }} me-1 mt-1">
                                        {{ $cType->label() }}
                                    </span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    @if(!$isClosed && !$isSystem && $comment->user_id === auth()->id())
                                    <button type="button"
                                        class="btn btn-ghost-danger btn-icon btn-sm"
                                        wire:click="deleteComment({{ $comment->id }})"
                                        wire:confirm="هل أنت متأكد من حذف هذا التعليق؟">
                                        <i class="ri-delete-bin-line fs-14"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div class="p-3 rounded bg-light {{ $isClosed ? 'border border-danger-subtle fw-medium' : '' }}">
                                {!! nl2br(e($comment->body)) !!}
                            </div>
                            <div class="mt-1">
                                <small class="text-muted">{{ $comment->created_at->format('Y-m-d h:i A') }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                            colors="primary:#121331,secondary:#08a88a"
                            style="width:75px;height:75px">
                        </lord-icon>
                        <h6 class="mt-3 text-muted">لا توجد تعليقات بعد</h6>
                        <p class="text-muted fs-13">ابدأ بإضافة أول تعليق أو نشاط لهذه الفرصة البيعية</p>
                    </div>
                    @endforelse
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
                Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    })
                    .fire({
                        icon: type,
                        title: message
                    });
            });
        });
    </script>
    @endpush
</div>