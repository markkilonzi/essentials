@props(['columns', 'rows'])

<div class="my-5 overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
    <table class="w-full min-w-120 text-left text-sm">
        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
            <tr>
                @foreach ($columns as $column)
                    <th class="px-4 py-2.5">{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($rows as $row)
                <tr class="align-top">
                    @foreach ($row as $cell)
                        @if ($loop->first)
                            <td class="px-4 py-2.5"><code class="rounded bg-primary/10 px-1.5 py-0.5 text-[13px] text-primary-darker">{{ $cell }}</code></td>
                        @else
                            <td class="px-4 py-2.5 text-gray-600">{{ $cell }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
