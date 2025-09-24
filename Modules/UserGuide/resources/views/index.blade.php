@extends('userguide::layouts.app')

@section('title', 'لوحة التحكم - فوتره')
@section('page_title', 'لوحة التحكم')
@section('page_subtitle', 'مرحبا بك')
@section('header_class', 'Dashboard')

@php
    $showPageHeader = true;
@endphp

@section('content')
    <div class="pages-view">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-12 col-content">
                    <!-- Site Block -->
                    <div class="top-action-section">
                        <div class="row-header">
                            <div class="account-header">
                                <span class="company-name-text">{{ $company['name'] ?? 'meta code' }}</span>
                                <span class="account-id-badge">#{{ $company['id'] ?? '4367919' }}</span>
                            </div>
                            <div class="action-buttons-left">
                                <button class="btn-login-green">
                                    دخول
                                </button>
                                <button class="btn-upgrade-blue">
                                    +ترقية
                                </button>
                            </div>
                        </div>
                        <div class="account-info-right">

                            <a href="{{ $company['url'] ?? 'https://alfakhrealhomsi.daftra.com' }}" class="website-link"
                                target="_blank">
                                {{ $company['url'] ?? 'https://alfakhrealhomsi.daftra.com' }}
                            </a>
                            <div class="expiry-info-section">
                                <button class="renew-btn">
                                    تجديد
                                </button>
                                <span class="expiry-badge">
                                    تنتهي في {{ $company['expiry_date'] ?? '9 أغسطس 2025' }}
                                </span>
                                <div class="text-right col-sm-6">
                                    <div class="dropdown">
                                        <span class="" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </span>
                                        <div class="dropdown-menu text-center" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item btn-sm text-danger d-flex justify-content-center align-items-center"
                                                href="#" style="font-size: 12px">
                                                <i class="fas fa-times mx-1"></i>
                                                <span>إلغاء الحساب</span>
                                            </a>
                                            <a class="dropdown-item btn-sm text-warning d-flex justify-content-center align-items-center"
                                                href="#">
                                                <i class="fas fa-sync-alt mx-1"></i>
                                                <span>تصفير بيانات الحساب</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    @include('userguide::layouts.payment')
                </div>

                <!-- Sidebar -->
                @include('userguide::layouts.sidebar')

                <div class="clear"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endsection
