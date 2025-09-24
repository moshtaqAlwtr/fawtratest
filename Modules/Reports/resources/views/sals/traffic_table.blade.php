@foreach($groups as $group)
    @php
        $clients = $group->neighborhoods
            ->flatMap(function ($neigh) { return $neigh->client ? [$neigh->client] : []; })
            ->filter()
            ->unique('id');
    @endphp

    @if($clients->count() > 0)
        <div class="card card-outline card-info mb-2 group-section" id="group-{{ $group->id }}">
            <!-- محتوى المجموعة والعملاء -->
            @foreach($clients as $client)
                <tr class="client-row">
                    <td>{{ $client->trade_name }}</td>
                    @foreach($weeks as $week)
                        @php
                            // حساب النشاط لكل أسبوع
                            $activities = $this->calculateActivities($client, $week);
                        @endphp
                        <td class="{{ $activities['cell_class'] }}">
                            <!-- عرض أيقونات النشاط -->
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </div>
    @endif
@endforeach