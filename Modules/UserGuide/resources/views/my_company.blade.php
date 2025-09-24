@extends('userguide::layouts.app')

@section('title', 'لوحة التحكم - فوتره')
@section('page_title', 'لوحة التحكم')
@section('page_subtitle', 'مرحبا بك')
@section('header_class', 'Dashboard')

@php
    $showPageHeader = true;
@endphp

@section('content')
    <style>
        .rtl-text {
            direction: rtl;
            text-align: right;
        }
    </style>

    <div class="pages-view" dir="rtl">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-12 col-content">
                    @switch($page)
                        @case('url_company')
                            @include('userguide::layouts.url_my_company')
                        @break

                        @case('referrals')
                            @include('userguide::layouts.registered_referrals')
                        @break

                        @case('account_statement')
                            @include('userguide::layouts.account_statement')
                        @break

                        @case('activate_coupon')
                            @include('userguide::layouts.activate_coupon')
                        @break

                        @default
                            <h1> {{ $page }}</h1>
                    @endswitch
                </div>
                <!-- Sidebar -->
                @include('userguide::layouts.sidebar')

                <div class="clear"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endsection
