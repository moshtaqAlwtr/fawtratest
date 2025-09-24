<link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">
<style>
    .section-header {
        cursor: pointer;
        font-weight: bold;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .status-completed {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-active {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    .status-in-progress {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }
    .status-closed {
        background-color: #6c757d;
        color: white;
        border: 1px solid #5a6268;
    }
    .table-hover-effect {
        background-color: #f8f9fa !important;
        transform: scale(1.01);
        transition: all 0.2s ease;
    }
    .timeline {
        list-style: none;
        padding: 0;
        position: relative;
    }
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
        left: 20px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        padding-left: 50px;
    }
    .timeline-item:before {
        content: '';
        position: absolute;
        left: 15px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #007bff;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #dee2e6;
    }
    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .timeline-day {
        font-weight: bold;
        color: #495057;
        margin: 20px 0 10px 0;
        padding: 10px;
        background: #e9ecef;
        border-radius: 5px;
    }
    .time {
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 5px;
    }
    .action-btn {
        margin: 2px;
    }
    .materials-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .materials-header {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        padding: 15px;
        border-bottom: 1px solid #e3e6f0;
        border-radius: 8px 8px 0 0;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }
</style>