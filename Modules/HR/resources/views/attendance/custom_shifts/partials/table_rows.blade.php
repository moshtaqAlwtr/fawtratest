@if(isset($custom_shifts) && !@empty($custom_shifts) && $custom_shifts->count() > 0)
    @foreach ($custom_shifts as $custom_shift)
        <tr>
            <td>{{ $custom_shift->name }}</td>
            <td>{{ $custom_shift->shift->name }}</td>
            <td>{{ $custom_shift->from_date }}</td>
            <td>{{ $custom_shift->to_date }}</td>
            <td>
                <div class="btn-group">
                    <div class="dropdown">
                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm" type="button" id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                            <li>
                                <a class="dropdown-item" href="{{ route('custom_shifts.show', $custom_shift->id) }}">
                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('custom_shifts.edit', $custom_shift->id) }}">
                                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $custom_shift->id }}">
                                    <i class="fa fa-trash me-2"></i>حذف
                                </a>
                            </li>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
@endif
