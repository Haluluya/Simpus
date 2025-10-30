@props(['columns' => 5, 'rows' => 4])

@php
    $rowRange = range(1, $rows);
    $colRange = range(1, $columns);
@endphp

<div {{ $attributes->class('table-wrapper') }}>
    <table class="table">
        <thead>
        <tr>
            @foreach ($colRange as $index)
                <th>
                    <div class="skeleton-line h-3 w-20"></div>
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
        @foreach ($rowRange as $row)
            <tr>
                @foreach ($colRange as $col)
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="skeleton-circle h-9 w-9"></div>
                            <div class="flex flex-col gap-2">
                                <div class="skeleton-line h-3 w-32"></div>
                                <div class="skeleton-line h-3 w-24"></div>
                            </div>
                        </div>
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
