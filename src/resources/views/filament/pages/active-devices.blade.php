
<x-filament-panels::page>

    <div
        wire:poll.10s="pingDevices"
        class="space-y-6"
    >

        {{-- Estadísticas --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

            @foreach ([
                [
                    'label' => 'Dispositivos detectados',
                    'value' => $this->stats['total'],
                    'color' => 'text-primary-600 dark:text-primary-400',
                ],
                [
                    'label' => 'En línea',
                    'value' => $this->stats['online'],
                    'color' => 'text-success-600 dark:text-success-400',
                ],
                [
                    'label' => 'Sin respuesta',
                    'value' => $this->stats['offline'],
                    'color' => 'text-danger-600 dark:text-danger-400',
                ],
            ] as $stat)

                <x-filament::section>
                    <div class="space-y-2">

                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $stat['label'] }}
                        </p>

                        <p class="text-3xl font-bold {{ $stat['color'] }}">
                            {{ $stat['value'] }}
                        </p>

                    </div>
                </x-filament::section>

            @endforeach

        </div>

        {{-- Tabla de dispositivos --}}
        <x-filament::section>

            <x-slot name="heading">
                Network devices discovered
            </x-slot>

            <x-slot name="description">
                Automated monitoring tool (10s loop)
            </x-slot>

            <x-slot name="headerEnd">
    <div class="flex items-center gap-3">

        <select
            wire:model.live="sortBy"
            class="fi-input block rounded-lg border-gray-300 bg-white
                   text-sm text-gray-950 shadow-sm
                   focus:border-primary-500 focus:ring-primary-500
                   dark:border-gray-700 dark:bg-gray-900
                   dark:text-white"
        >
            <option value="ping_desc">Greater ping</option>
            <option value="ping_asc">Smallest ping</option>
        </select>

        <x-filament::button
            wire:click="refreshDevices"
            icon="heroicon-o-arrow-path"
            size="sm"
            color="gray"
        >
            Update
        </x-filament::button>

    </div>
</x-slot>

            <div class="overflow-x-auto">

                <table class="w-full divide-y divide-gray-200 text-left dark:divide-white/5">

                    <thead>
                        <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">

                            <th class="px-4 py-3">
                                Device
                            </th>

                            <th class="px-4 py-3">
                                IP Address
                            </th>

                            <th class="px-4 py-3">
                                MAC Address
                            </th>

                            <th class="px-4 py-3">
                                Version
                            </th>

                            <th class="px-4 py-3">
                                Interfaz
                            </th>

                            <th class="px-4 py-3 text-right">
                                Latency
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">

                        @forelse ($this->sortedDevices as $device)

                            @php
                                $ip = $device['address'] ?? null;
                                $ping = $device['ping'] ?? null;

                                $color = $ping === null
                                    ? 'danger'
                                    : ($ping <= 20
                                        ? 'success'
                                        : ($ping <= 50
                                            ? 'warning'
                                            : 'danger'));
                            @endphp

                            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-white/5">

                                {{-- Identidad --}}
                                <td class="px-4 py-3">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">

                                            @svg(
                                                'heroicon-o-server-stack',
                                                'h-5 w-5 text-gray-500 dark:text-gray-400'
                                            )

                                        </div>

                                        <div>
                                            <p class="text-sm font-semibold text-gray-950 dark:text-white">
                                                {{ $device['identity'] ?? 'Unknown Device' }}
                                            </p>

                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Network Device
                                            </p>
                                        </div>

                                    </div>

                                </td>

                                {{-- Dirección IP --}}
                                <td class="px-4 py-3">

                                    @if ($ip && filter_var($ip, FILTER_VALIDATE_IP))

                                        <a
                                            href="http://{{ $ip }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="font-mono text-sm font-medium text-primary-600 hover:underline dark:text-primary-400"
                                        >
                                            {{ $ip }}
                                        </a>

                                    @else

                                        <span class="text-sm text-gray-400">
                                            N/A
                                        </span>

                                    @endif

                                </td>

                                {{-- MAC --}}
                                <td class="px-4 py-3">

                                    <span class="font-mono text-xs text-gray-600 dark:text-gray-400">
                                        {{ $device['mac-address'] ?? 'N/A' }}
                                    </span>

                                </td>

                                {{-- Firmware --}}
                                <td class="px-4 py-3">

                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $device['version'] ?? 'N/A' }}
                                    </span>

                                </td>

                                {{-- Interface --}}
                                <td class="px-4 py-3">

                                    <x-filament::badge color="gray">
                                        {{ $device['interface'] ?? 'N/A' }}
                                    </x-filament::badge>

                                </td>

                                {{-- Latencia --}}
                                <td class="px-4 py-3 text-right">

                                    <x-filament::badge :color="$color">

                                        @if ($ping !== null)
                                            {{ number_format($ping, 2) }} ms
                                        @else
                                            Timeout
                                        @endif

                                    </x-filament::badge>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="6"
                                    class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400"
                                >
                                    No devices found
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>

            </div>

        </x-filament::section>

    </div>

</x-filament-panels::page>
