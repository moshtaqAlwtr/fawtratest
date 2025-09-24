@foreach ($children as $child)
    <div class="branch">
        <h4>{{ $child->name }} ({{ $child->code }})</h4>

        @if ($child->journalEntries->count() > 0)
            <ul>
                @foreach ($child->journalEntries as $entry)
                    <li>القيد: {{ $entry->description }} - المبلغ: {{ $entry->debit }}</li>
                @endforeach
            </ul>
        @else
            <p>لا توجد قيود لهذا الحساب.</p>
        @endif

        @if ($child->children->count() > 0)
            <div class="sub-branches">
                @include('accounts.partials.children', ['children' => $child->children])
            </div>
        @endif
    </div>
@endforeach