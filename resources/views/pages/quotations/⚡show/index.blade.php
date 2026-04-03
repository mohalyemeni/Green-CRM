<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between hide-on-print">
                <h4 class="mb-sm-0">استعراض تفاصيل العرض</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.quotations.index') }}">عروض الأسعار</a></li>
                        <li class="breadcrumb-item active">استعراض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9">
            <div class="card" id="printSection">
                <div class="card-body">
                    <div class="row mb-5 pb-3">
                        <div class="col-sm-6">
                            <div class="mb-4">
                                <h3 class="fw-bold mb-1">عرض سعر <span class="text-muted fw-normal ms-2">#{{ $quotation->code }}</span></h3>
                                <p class="text-muted fs-15">{{ $quotation->title }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <h4 class="fw-semibold text-primary text-uppercase">شعار المؤسسة</h4>
                                <p class="text-muted mb-1">شركة إيرا تيك لتقنية المعلومات</p>
                                <p class="text-muted mb-0">info@eratech.com | 0500000000</p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4 pt-4 border-top border-top-dashed">
                        <div class="col-sm-6">
                            <h6 class="text-muted text-uppercase fw-semibold mb-3">مقدم إلى:</h6>
                            <h5 class="fs-15 fw-bold mb-1">{{ $quotation->customer_name }}</h5>
                            @if($quotation->customer_phone)<p class="text-muted mb-1"><i class="ri-phone-fill me-1 align-bottom"></i> {{ $quotation->customer_phone }}</p>@endif
                            @if($quotation->customer_email)<p class="text-muted mb-1"><i class="ri-mail-fill me-1 align-bottom"></i> {{ $quotation->customer_email }}</p>@endif
                            @if($quotation->customer_address)<p class="text-muted mb-0" style="max-width: 250px;">{{ $quotation->customer_address }}</p>@endif
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <h6 class="text-muted text-uppercase fw-semibold mb-3">تفاصيل مستند العرض:</h6>
                            <p class="mb-1">تاريخ الإصدار: <span class="fw-medium">{{ $quotation->issue_date->format('Y-m-d') }}</span></p>
                            <p class="mb-1">صلاحية العرض: <span class="fw-medium text-danger">{{ $quotation->expiry_date->format('Y-m-d') }}</span></p>
                            <p class="mb-0">الحالة: 
                                <span class="badge bg-{{ $quotation->status->color() }}-subtle text-{{ $quotation->status->color() }} fs-11">
                                    {{ $quotation->status->label() }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-borderless align-middle mb-0">
                            <thead>
                                <tr class="table-active">
                                    <th scope="col">البند / التفاصيل</th>
                                    <th scope="col" class="text-center" style="width: 100px;">الكمية</th>
                                    <th scope="col" class="text-end" style="width: 120px;">السعر الإفرادي</th>
                                    <th scope="col" class="text-end" style="width: 120px;">الخصم</th>
                                    <th scope="col" class="text-end" style="width: 120px;">الضريبة</th>
                                    <th scope="col" class="text-end" style="width: 150px;">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody id="products-list">
                                @foreach($quotation->items as $item)
                                <tr>
                                    <td>
                                        <h6 class="mb-1 fs-14">{{ $item->item_name }}</h6>
                                        @if($item->description)
                                            <p class="text-muted fs-13 mb-0">{{ $item->description }}</p>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end text-danger">
                                        @if($item->discount_amount > 0)
                                            -{{ number_format($item->discount_amount, 2) }} @if($item->discount_type == 'percentage')%@endif
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($item->is_taxable && $item->tax_amount > 0)
                                            {{ number_format($item->tax_amount, 2) }} <small class="text-muted">({{ $item->tax_rate }}%)</small>
                                        @else
                                            <span class="text-muted">معفى</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="border-top border-top-dashed mt-3 pt-3">
                        <div class="row">
                            <div class="col-sm-6">
                                {{-- Notes and terms --}}
                                @if($quotation->terms_conditions)
                                <div class="mt-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-2">الشروط والأحكام:</h6>
                                    <p class="text-muted mb-0">{!! nl2br(e($quotation->terms_conditions)) !!}</p>
                                </div>
                                @endif
                                @if($quotation->customer_notes)
                                <div class="mt-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-2">ملاحظات للعميل:</h6>
                                    <p class="text-muted mb-0">{!! nl2br(e($quotation->customer_notes)) !!}</p>
                                </div>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <table class="table table-borderless table-sm fw-medium align-middle mb-0 text-end">
                                    <tbody>
                                        <tr>
                                            <td>المجموع الفرعي:</td>
                                            <td><span class="fs-14">{{ number_format($quotation->subtotal, 2) }}</span></td>
                                        </tr>
                                        @if($quotation->discount_amount > 0)
                                        <tr>
                                            <td>الخصم الإضافي: 
                                                @if($quotation->discount_type == 'percentage')
                                                    <small class="text-muted">({{ number_format($quotation->discount_amount, 2) }}%)</small>
                                                @endif
                                            </td>
                                            <td class="text-danger">- 
                                                @if($quotation->discount_type == 'percentage')
                                                    {{ number_format($quotation->subtotal * ($quotation->discount_amount / 100), 2) }}
                                                @else
                                                    {{ number_format($quotation->discount_amount, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>إجمالي ضريبة القيمة المضافة:</td>
                                            <td><span class="fs-14">{{ number_format($quotation->tax_amount, 2) }}</span></td>
                                        </tr>
                                        <tr class="border-top border-top-dashed mt-2 pt-2">
                                            <th scope="row" class="fs-16">إجمالي العرض المُستحق:</th>
                                            <th><span class="fs-16 text-success">{{ number_format($quotation->total, 2) }}</span></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 mb-2 text-center text-muted fs-13">
                        هذا العرض مُنشأ آلياً ولا يتطلب توقيعاً أو ختماً.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 hide-on-print">
            <div class="card">
                <div class="card-header border-bottom border-light">
                    <h5 class="card-title mb-0"><i class="ri-settings-4-line align-middle me-1"></i> الإجراءات</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button onclick="window.print()" class="btn btn-secondary">
                            <i class="ri-printer-line align-bottom me-1"></i> طباعة الفاتورة
                        </button>
                        <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="btn btn-primary">
                            <i class="ri-pencil-fill align-bottom me-1"></i> تعديل العرض
                        </a>
                        <button class="btn btn-soft-success">
                            <i class="ri-download-2-line align-bottom me-1"></i> تحميل PDF
                        </button>
                    </div>
                </div>
            </div>

            {{-- Activities Timeline --}}
            <div class="card mt-3">
                <div class="card-header align-items-center d-flex border-bottom border-light">
                    <h4 class="card-title mb-0 flex-grow-1"><i class="ri-history-line align-middle me-1"></i> سجل الحركات</h4>
                </div>
                <div class="card-body">
                    <div class="profile-timeline">
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            @foreach($activities as $act)
                            <div class="accordion-item border-0">
                                <div class="accordion-header" id="heading{{ $act->id }}">
                                    <button class="accordion-button fs-13 accordion-button accordion-button-custom accordion-icon-none p-0 pb-3 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $act->id }}" aria-expanded="true" aria-controls="collapse{{ $act->id }}">
                                        <div class="ms-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="mb-1 text-truncate fs-13">{{ $act->description ?? 'تحديث بيانات' }}</h6>
                                                @if($act->action == 'created')
                                                    <span class="badge bg-success-subtle text-success ms-auto fs-10">إنشاء</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning ms-auto fs-10">تحديث</span>
                                                @endif
                                            </div>
                                            <p class="text-muted mb-0 fs-11">{{ $act->created_at->diffForHumans() }} بواسطة <span class="fw-semibold">{{ $act->user?->name ?? 'النظام' }}</span></p>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                            @if(count($activities) == 0)
                                <div class="text-center text-muted py-3">لا توجد حركات مسجلة</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @if($quotation->notes)
            <div class="card bg-warning-subtle border-warning mt-3">
                <div class="card-body">
                    <h5 class="fs-13 text-warning"><i class="ri-error-warning-line align-bottom me-1"></i> ملاحظات الإدارة:</h5>
                    <p class="mb-0 text-muted fs-12">{{ $quotation->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .hide-on-print {
                display: none !important;
            }
            #printSection, #printSection * {
                visibility: visible;
            }
            #printSection {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none !important;
                border: none !important;
            }
            #printSection .card-body {
                padding: 10px !important;
            }
            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>
    @endpush
</div>
