@extends('master')

@section('title')
خزائن وحسابات بنكية
@stop

@section('content')

<div class="container mt-4">
    <h2 class="text-center mb-4">تحويل بين الخزائن</h2>
    <form action="{{ route('treasury.transfer') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header text-center bg-primary text-white">تحويل بين الخزائن</div>
            <div class="card-body">
                <div class="row">
                    <!-- اختيار الخزائن -->
                    <div class="col-md-6">
                        <label>من خزينة:</label>
                        <select id="from_treasury_id" name="from_treasury_id" class="form-control" required>
                            <option value="">اختر خزينة</option>
                            @foreach($treasuries as $treasury)
                                <option value="{{ $treasury->id }}" data-balance="{{ $treasury->balance }}">
                                    {{ $treasury->name }} ({{ $treasury->balance }} ريال)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>إلى خزينة:</label>
                        <select id="to_treasury_id" name="to_treasury_id" class="form-control" required>
                            <option value="">اختر خزينة</option>
                            @foreach($treasuries as $treasury)
                                <option value="{{ $treasury->id }}" data-balance="{{ $treasury->balance }}">
                                    {{ $treasury->name }} ({{ $treasury->balance }} ريال)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- المتاح قبل التحويل -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>المتاح قبل:</label>
                        <input type="text" id="from_balance_before" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>المتاح قبل:</label>
                        <input type="text" id="to_balance_before" class="form-control" readonly>
                    </div>
                </div>

                <!-- مبلغ التحويل -->
                <div class="row mt-3">
                    <div class="col-md-12 text-center">
                        <label>المبلغ:</label>
                        <input type="number" id="amount" name="amount" class="form-control text-center" step="0.01" required>
                    </div>
                </div>

                <!-- المتاح بعد التحويل -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>المتاح بعد:</label>
                        <input type="text" id="from_balance_after" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>المتاح بعد:</label>
                        <input type="text" id="to_balance_after" class="form-control" readonly>
                    </div>
                </div>

                <!-- زر الإرسال -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">إجراء التحويل</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- سكريبت لحساب المتاح بعد -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    let fromSelect = document.getElementById("from_treasury_id");
    let toSelect = document.getElementById("to_treasury_id");
    let amountInput = document.getElementById("amount");

    function updateBalances() {
        let fromBalanceBefore = parseFloat(fromSelect.selectedOptions[0]?.dataset.balance || 0);
        let toBalanceBefore = parseFloat(toSelect.selectedOptions[0]?.dataset.balance || 0);
        let amount = parseFloat(amountInput.value) || 0;

        document.getElementById("from_balance_before").value = fromBalanceBefore.toFixed(2) + " ريال";
        document.getElementById("to_balance_before").value = toBalanceBefore.toFixed(2) + " ريال";

        document.getElementById("from_balance_after").value = (fromBalanceBefore - amount).toFixed(2) + " ريال";
        document.getElementById("to_balance_after").value = (toBalanceBefore + amount).toFixed(2) + " ريال";
    }

    fromSelect.addEventListener("change", updateBalances);
    toSelect.addEventListener("change", updateBalances);
    amountInput.addEventListener("input", updateBalances);
});
</script>

@endsection
