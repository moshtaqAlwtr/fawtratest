@extends('userguide::layouts.app')

@section('title', 'لوحة التحكم - فوتره')
@section('page_title', 'لوحة التحكم')
@section('page_subtitle', 'مرحبا بك')
@section('header_class', 'Dashboard')

@php
    $showPageHeader = true;
@endphp
<style>
    .rtl-text {
        direction: rtl;
        text-align: right;
    }
</style>
@section('content')
    <div class="pages-view" dir="rtl">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-12 col-content">
                    @switch($page)
                        @case('change_email')
                            @include('userguide::layouts.change_email')
                        @break

                        @case('change_password')
                            @include('userguide::layouts.change_password')
                        @break

                        @case('payment_settings')
                            @include('userguide::layouts.payment_settings')
                        @break

                        @default
                            @include('userguide::layouts.change_email')
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
