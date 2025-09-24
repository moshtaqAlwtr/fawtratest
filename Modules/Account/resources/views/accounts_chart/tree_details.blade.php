<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>العملية</th>
                        <th>مدين</th>
                        <th>دائن</th>
                        <th>الرصيد بعد</th>
                        <th>التاريخ</th>
                        <th>الاجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($operationsPaginator as $operation)
                        <tr>
                            <td>{{ $operation['operation'] }}</td>
                            <td>{{ number_format($operation['deposit'], 2) }}</td>
                            <td>{{ number_format($operation['withdraw'], 2) }}</td>
                            <td>{{ number_format($operation['balance_after'], 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($operation['date'])->format('Y-m-d') }}</td>

                            <td>   
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="{{ route('journal.show', $operation['journalEntry']) }}">
                                                <i class="fa fa-eye me-2 text-primary"></i>عرض
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
