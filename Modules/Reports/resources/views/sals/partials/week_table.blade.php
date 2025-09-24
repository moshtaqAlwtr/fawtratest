<table class="table table-bordered text-center">
    <thead>
        <tr>
            <th>المجموعة</th>
            <th>الحي</th>
            <th>العميل</th>
            @foreach($weeks as $week)
                <th>
                    أسبوع {{ $week['week_number'] }}<br>
                    {{ \Carbon\Carbon::parse($week['start'])->format('d M') }} - {{ \Carbon\Carbon::parse($week['end'])->format('d M') }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($groups as $group)
            @foreach($group->neighborhoods as $neighborhood)
                @if ($neighborhood->client)
                    @php $client = $neighborhood->client; @endphp
                    <tr>
                        <td>{{ $group->name }}</td>
                        <td>{{ $neighborhood->name }}</td>
                        <td>{{ $client->name }}</td>

                        @foreach($weeks as $week)
                            @php
                                $activities = [];
                                $start = $week['start'];
                                $end = $week['end'];

                                $visits = $client->visits->whereBetween('visit_date', [$start, $end]);
                                foreach ($visits as $visit) {
                                    $activities[] = '<i class="fas fa-user-check text-success"></i>';
                                }

                                $payments = $client->payments->whereBetween('payment_date', [$start, $end]);
                                foreach ($payments as $payment) {
                                    $activities[] = '<i class="fas fa-money-bill-alt text-primary"></i>';
                                }

                                $receipts = $client->accounts->flatMap(function ($account) {
                                    return $account->receipts;
                                })->whereBetween('created_at', [$start, $end]);

                                foreach ($receipts as $receipt) {
                                    $activities[] = '<i class="fas fa-receipt text-warning"></i>';
                                }

                                $notes = $client->appointmentNotes->whereBetween('created_at', [$start, $end]);
                                foreach ($notes as $note) {
                                    $activities[] = '<i class="fas fa-sticky-note text-muted"></i>';
                                }
                            @endphp

                            <td>{!! implode(' ', $activities) !!}</td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>
