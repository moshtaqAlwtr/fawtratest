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
