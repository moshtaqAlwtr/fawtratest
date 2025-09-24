   @if (@isset($appointments) && !@empty($appointments) && count($appointments) > 0)

       <div class="table-responsive">
           <table class="table">
               <thead class="bg-light">
                   <tr>
                       <th class="min-mobile">اسم العميل</th>
                       <th class="min-tablet">حالة العميل</th>
                       <th class="min-tablet">رقم الهاتف</th>
                       <th class="min-mobile">التاريخ</th>
                       <th class="min-tablet">الوقت</th>
                       <th class="min-desktop">المدة</th>
                       <th class="min-tablet">الموظف</th>
                       <th class="min-mobile">الحالة</th>
                       <th style="width: 120px">الإجراءات</th>
                   </tr>
               </thead>
               <tbody>
                   @foreach ($appointments as $info)
                       <tr>
                           <td class="min-mobile">{{ $info->client->trade_name }}</td>
                           <td class="min-tablet">
                               @if ($info->client->status_client)
                                   <span
                                       style="background-color: {{ $info->client->status_client->color }}; color: #fff; padding: 2px 8px; font-size: 12px; border-radius: 4px; display: inline-block;">
                                       {{ $info->client->status_client->name }}
                                   </span>
                               @else
                                   <span
                                       style="background-color: #6c757d; color: #fff; padding: 2px 8px; font-size: 12px; border-radius: 4px; display: inline-block;">
                                       غير محدد
                                   </span>
                               @endif
                           </td>
                           <td class="min-tablet">{{ $info->client->phone }}</td>
                           <td class="min-mobile">
                               {{ \Carbon\Carbon::parse($info->appointment_date)->format('Y-m-d') }}</td>
                           <td class="min-tablet">{{ $info->time }}</td>
                           <td class="min-desktop">{{ $info->duration ?? 'غير محدد' }}</td>
                           <td class="min-tablet">
                               {{ $info->createdBy ? $info->createdBy->name : 'غير محدد' }}</td>
                           <td class="min-mobile">
                               <span
                                   class="badge {{ $info->status == 1 ? 'bg-warning' : ($info->status == 2 ? 'bg-success' : ($info->status == 3 ? 'bg-danger' : 'bg-info')) }}">
                                   {{ $info->status == 1 ? 'قيد الانتظار' : ($info->status == 2 ? 'مكتمل' : ($info->status == 3 ? 'ملغي' : 'معاد جدولته')) }}
                               </span>
                           </td>
                           <td>
                               <div class="btn-group dropstart">
                                   <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v " type="button"
                                       id="dropdownMenuButton{{ $info->id }}" data-bs-toggle="dropdown"
                                       aria-expanded="false">
                                   </button>
                                   <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $info->id }}">
                                       <li>
                                           <a class="dropdown-item" href="{{ route('appointments.edit', $info->id) }}">
                                               <i class="fa fa-edit me-2 text-success"></i>تعديل
                                           </a>
                                       </li>
                                       <li>
                                           <form action="{{ route('appointments.update-status', $info->id) }}"
                                               method="POST" class="d-inline">
                                               @csrf
                                               @method('PATCH')
                                               <input type="hidden" name="status" value="1">
                                               <button type="submit" class="dropdown-item">
                                                   <i class="fa fa-clock me-2 text-warning"></i>تم جدولته
                                               </button>
                                           </form>
                                       </li>
                                       <li>
                                           <form action="{{ route('appointments.update-status', $info->id) }}"
                                               method="POST" class="d-inline">
                                               @csrf
                                               @method('PATCH')
                                               <input type="hidden" name="status" value="2">
                                               <input type="hidden" name="auto_delete" value="1">
                                               <button type="submit" class="dropdown-item">
                                                   <i class="fa fa-check me-2 text-success"></i>تم
                                               </button>
                                           </form>
                                       </li>
                                       <li>
                                           <form action="{{ route('appointments.update-status', $info->id) }}"
                                               method="POST" class="d-inline">
                                               @csrf
                                               @method('PATCH')
                                               <input type="hidden" name="status" value="3">
                                               <button type="submit" class="dropdown-item">
                                                   <i class="fa fa-times me-2 text-danger"></i>صرف النظر عنه
                                               </button>
                                           </form>
                                       </li>
                                       <li>
                                           <form action="{{ route('appointments.update-status', $info->id) }}"
                                               method="POST" class="d-inline">
                                               @csrf
                                               @method('PATCH')
                                               <input type="hidden" name="status" value="4">
                                               <button type="submit" class="dropdown-item">
                                                   <i class="fa fa-redo me-2 text-info"></i>تم جدولته مجددا
                                               </button>
                                           </form>
                                       </li>
                                       <li>
                                           <form action="{{ route('appointments.destroy', $info->id) }}" method="POST"
                                               class="d-inline">
                                               @csrf
                                               @method('DELETE')
                                               <button type="submit" class="dropdown-item text-danger"
                                                   onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')">
                                                   <i class="fa fa-trash me-2"></i>حذف
                                               </button>
                                           </form>
                                       </li>
                                   </ul>
                               </div>
                           </td>
                       </tr>
                   @endforeach
               </tbody>
           </table>
       </div>
   @else
       <div class="alert alert-info text-center">
           <p class="mb-0">لا توجد مواعيد مسجلة حالياً</p>
       </div>
   @endif

   <style>
       .dropdown-menu {
           position: absolute !important;
           z-index: 1000 !important;
           min-width: 10rem !important;
           padding: 0.5rem 0 !important;
           margin: 0 !important;
           font-size: 1rem !important;
           color: #212529 !important;
           text-align: right !important;
           list-style: none !important;
           background-color: #fff !important;
           background-clip: padding-box !important;
           border: 1px solid rgba(0, 0, 0, 0.15) !important;
           border-radius: 0.25rem !important;
           box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175) !important;
       }

       .dropdown-item {
           display: block !important;
           width: 100% !important;
           padding: 0.5rem 1.5rem !important;
           clear: both !important;
           font-weight: 400 !important;
           color: #212529 !important;
           text-align: inherit !important;
           text-decoration: none !important;
           white-space: nowrap !important;
           background-color: transparent !important;
           border: 0 !important;
       }

       .dropdown-item:hover {
           background-color: #f8f9fa !important;
       }
   </style>
