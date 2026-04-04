@extends('layouts.admin')
@section('title', 'لوحة التحكم')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">نظرة عامة على النظام</h4>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- العملاء المحتملين --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-animate border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fw-medium text-muted mb-0">العملاء المحتملين (Leads)</p>
                        <h2 class="mt-2 ff-secondary fw-semibold mb-0 text-primary">
                            {{ number_format($stats['leads']) }}
                        </h2>
                        <p class="mb-0 text-muted fs-12 mt-1">
                            إجمالي العملاء المحتملين المسجلين
                        </p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle text-primary rounded-3 fs-26">
                            <i class="ri-user-star-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- العملاء --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-animate border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fw-medium text-muted mb-0">العملاء (Customers)</p>
                        <h2 class="mt-2 ff-secondary fw-semibold mb-0 text-success">
                            {{ number_format($stats['customers']) }}
                        </h2>
                        <p class="mb-0 text-muted fs-12 mt-1">
                            إجمالي العملاء الفعليين
                        </p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle text-success rounded-3 fs-26">
                            <i class="ri-team-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- الفرص البيعية --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-animate border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fw-medium text-muted mb-0">إجمالي الفرص البيعية</p>
                        <h2 class="mt-2 ff-secondary fw-semibold mb-0 text-warning">
                            {{ number_format($stats['opportunities']) }}
                        </h2>
                        <p class="mb-0 text-muted fs-12 mt-1">
                            جميع الفرص المفتوحة والمغلقة
                        </p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle text-warning rounded-3 fs-26">
                            <i class="ri-funds-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- الإيرادات المتوقعة / المحققة --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-animate border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fw-medium text-muted mb-0">إيرادات الفوز (تقريبية)</p>
                        <h2 class="mt-2 ff-secondary fw-semibold mb-0 text-info">
                            {{ number_format($stats['total_revenue']) }}
                        </h2>
                        <p class="mb-0 text-muted fs-12 mt-1">
                            مجموع إيرادات الفرص الناجحة
                        </p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle text-info rounded-3 fs-26">
                            <i class="ri-money-dollar-circle-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- الفرص المغلقة بفوز --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-animate border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fw-medium text-muted mb-0">الفرص الناجحة (Won)</p>
                        <h2 class="mt-2 ff-secondary fw-semibold mb-0" style="color: #0ab39c;">
                            {{ number_format($stats['won_opportunities']) }}
                        </h2>
                        <p class="mb-0 text-muted fs-12 mt-1">
                            الفرص المغلقة بفوز
                        </p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title rounded-3 fs-26" style="background-color: rgba(10, 179, 156, 0.1); color: #0ab39c;">
                            <i class="ri-checkbox-circle-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- الفرص المغلقة بخسارة --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-animate border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fw-medium text-muted mb-0">الفرص الخاسرة (Lost)</p>
                        <h2 class="mt-2 ff-secondary fw-semibold mb-0 text-danger">
                            {{ number_format($stats['lost_opportunities']) }}
                        </h2>
                        <p class="mb-0 text-muted fs-12 mt-1">
                            الفرص المغلقة بخسارة
                        </p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-danger-subtle text-danger rounded-3 fs-26">
                            <i class="ri-close-circle-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- عروض الأسعار --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-animate border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fw-medium text-muted mb-0">عروض الأسعار</p>
                        <h2 class="mt-2 ff-secondary fw-semibold mb-0 text-secondary">
                            {{ number_format($stats['quotations']) }}
                        </h2>
                        <p class="mb-0 text-muted fs-12 mt-1">
                            إجمالي عروض الأسعار الصادرة
                        </p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-secondary-subtle text-secondary rounded-3 fs-26">
                            <i class="ri-file-list-3-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- الخدمات --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-animate border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fw-medium text-muted mb-0">الخدمات المتاحة</p>
                        <h2 class="mt-2 ff-secondary fw-semibold mb-0 text-dark">
                            {{ number_format($stats['services']) }}
                        </h2>
                        <p class="mb-0 text-muted fs-12 mt-1">
                            إجمالي الخدمات المعرفة بالنظام
                        </p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-dark-subtle text-dark rounded-3 fs-26">
                            <i class="ri-customer-service-2-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection