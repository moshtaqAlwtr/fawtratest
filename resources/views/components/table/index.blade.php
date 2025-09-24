@props([
    'headers' => [], // عناوين الأعمدة
    'showCheckbox' => false, // إظهار خانة الاختيار
    'showActions' => true, // إظهار عمود الإجراءات
    'actions' => [], // الإجراءات المتاحة
    'tableClass' => 'table table-hover', // classes إضافية للجدول
    'cardTitle' => null, // عنوان الكارد
    'items' => [], // العناصر
    'showFooter' => true, // إظهار تذييل الجدول
    'footerText' => null, // نص التذييل
    'currentPage' => 1, // الصفحة الحالية
    'totalItems' => 0, // إجمالي العناصر
])

<x-layout.card :title="$cardTitle">
    <div class="table-responsive">
        <table class="{{ $tableClass }}">
            <thead>
                <tr>
                    @if($showCheckbox)
                        <th class="text-end" style="width: 40px">
                            <input type="checkbox" class="select-all">
                        </th>
                    @endif
                    @foreach($headers as $header)
                        <th class="text-end">{{ $header }}</th>
                    @endforeach
                    @if($showActions)
                        <th class="text-end" style="width: 10%">الإجراءات</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        @if($showCheckbox)
                            <td>
                                <input type="checkbox" class="select-item" value="{{ $item['id'] ?? '' }}">
                            </td>
                        @endif
                        @foreach($item['columns'] as $column)
                            <td>
                                @if(is_array($column))
                                    <div>{{ $column['main'] }}</div>
                                    @if(isset($column['sub']))
                                        <small class="text-muted">{{ $column['sub'] }}</small>
                                    @endif
                                @else
                                    {{ $column }}
                                @endif
                            </td>
                        @endforeach
                        @if($showActions)
                            <td>
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                            type="button" 
                                            id="dropdownMenuButton{{ $item['id'] ?? '' }}" 
                                            data-toggle="dropdown"
                                            aria-haspopup="true" 
                                            aria-expanded="false">
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $item['id'] ?? '' }}">
                                            @foreach($actions as $action)
                                                <li>
                                                    <a class="dropdown-item {{ $action['class'] ?? '' }}" 
                                                       href="{{ $action['url'] }}"
                                                       @if(isset($action['attributes']))
                                                           @foreach($action['attributes'] as $attr => $value)
                                                               {{ $attr }}="{{ $value }}"
                                                           @endforeach
                                                       @endif
                                                    >
                                                        <i class="fa {{ $action['icon'] }} me-2 {{ $action['iconClass'] ?? '' }}"></i>
                                                        {{ $action['label'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + ($showCheckbox ? 1 : 0) + ($showActions ? 1 : 0) }}" class="text-center">
                            لا توجد بيانات
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showFooter)
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                {{ $footerText ?? sprintf('%d-%d من %d النتائج المعروضة', 
                    ($currentPage - 1) * 15 + 1,
                    min($currentPage * 15, $totalItems),
                    $totalItems
                ) }}
            </div>
            @if(isset($paginator))
                <div>
                    {{ $paginator->links() }}
                </div>
            @endif
        </div>
    @endif
</x-layout.card>
