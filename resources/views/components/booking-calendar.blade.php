@php
    $first = $weeks[0][0]['date'];
    $last = end($weeks)[6]['date'];
@endphp
<div class="overflow-x-auto">
    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
        {{ $first->isoFormat('D. MMM') }} – {{ $last->isoFormat('D. MMM YYYY') }}
    </p>
    <table class="hk-agenda__month">
        <thead>
            <tr>
                @foreach (['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'] as $label)
                    <th scope="col" class="hk-agenda__label font-normal">{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($weeks as $week)
                <tr class="hk-agenda__week">
                    @foreach ($week as $day)
                        @php
                            $other = $day['event'] && ! $day['event']->is($event) ? $day['event'] : null;
                            $weekend = $day['state'] === 'FF' ? ($day['date']->isSaturday() ? ' hk-agenda__day--sat' : ($day['date']->isSunday() ? ' hk-agenda__day--sun' : '')) : '';
                            // Erster Tag und Monatsanfang mit Monat, damit der Monatswechsel erkennbar ist
                            $label = $loop->parent->first && $loop->first || $day['date']->day === 1 ? $day['date']->format('j.n.') : $day['date']->day;
                        @endphp
                        <td class="hk-agenda__day--{{ $day['state'] }}{{ $weekend }}" data-date="{{ $day['date']->toDateString() }}"
                            @if ($other) title="{{ $other->firstname }} {{ $other->name }}{{ $other->group_name ? ' ('.$other->group_name.')' : '' }}" @endif>
                            @if ($other)
                                <a href="{{ route('admin.events.edit', $other) }}" class="flex items-center justify-center w-full h-full">{{ $label }}</a>
                            @else
                                {{ $label }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
