<!-- Menü unten (nur Handy) -->
@php
  $bottomNavItems = [
    ['label' => 'Dashboard', 'href' => '/admin', 'active' => request()->is('admin'), 'icon' => 'm4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5'],
    ['label' => 'Zur Seite', 'href' => '/', 'active' => false, 'external' => true, 'icon' => 'M18 14v4.833A1.166 1.166 0 0 1 16.833 20H5.167A1.167 1.167 0 0 1 4 18.833V7.167A1.166 1.166 0 0 1 5.167 6h4.618m4.447-2H20v5.768m-7.889 2.121 7.778-7.778'],
  ];
  if (Auth::user()->canManageBookings()) {
    $bottomNavItems[] = ['label' => 'Anfragen', 'href' => route('admin.contacts.index'), 'active' => request()->routeIs('admin.contacts.*'), 'badge' => $openContactsCount, 'icon' => 'M14.7141 15h4.268c.4043 0 .732-.3838.732-.8571V3.85714c0-.47338-.3277-.85714-.732-.85714H6.71411c-.55228 0-1 .44772-1 1v4m10.99999 7v-3h3v3h-3Zm-3 6H6.71411c-.55228 0-1-.4477-1-1 0-1.6569 1.34315-3 3-3h2.99999c1.6569 0 3 1.3431 3 3 0 .5523-.4477 1-1 1Zm-1-9.5c0 1.3807-1.1193 2.5-2.5 2.5s-2.49999-1.1193-2.49999-2.5S8.8334 9 10.2141 9s2.5 1.1193 2.5 2.5Z'];
  }
  $bottomNavItems[] = ['label' => 'Buchungen', 'href' => route('admin.events.index'), 'active' => request()->routeIs('admin.events.*'), 'icon' => 'M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z'];
@endphp
<nav id="bottom-navigation" aria-label="Schnellmenü" class="fixed inset-x-0 bottom-0 z-40 border-t border-norway bg-norway pb-[env(safe-area-inset-bottom)] md:hidden dark:border-gray-700 dark:bg-gray-800">
  <ul class="grid" style="grid-template-columns: repeat({{ count($bottomNavItems) }}, minmax(0, 1fr))">
    @foreach ($bottomNavItems as $item)
      <li>
        <a href="{{ $item['href'] }}" @if($item['external'] ?? false) target="blank" @endif @if($item['active']) aria-current="page" @endif
          @class([
            'flex flex-col items-center gap-0.5 py-2 text-xs font-medium',
            'text-gray-900 dark:text-white' => $item['active'],
            'text-gray-700 dark:text-gray-400' => ! $item['active'],
          ])>
          <span class="relative">
            <svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $item['active'] ? 2.5 : 2 }}" d="{{ $item['icon'] }}"/>
            </svg>
            @if (($item['badge'] ?? 0) > 0)
              <span class="absolute -top-1.5 -right-2.5 min-w-4 rounded-full bg-orientalpink px-1 text-center text-[10px] leading-4 text-white">{{ $item['badge'] }}</span>
            @endif
          </span>
          {{ $item['label'] }}
        </a>
      </li>
    @endforeach
  </ul>
</nav>
