<x-filament-widgets::widget>

    <x-filament::section>

        <x-slot name="heading">
            MikroTik Interface Status
        </x-slot>

        <x-slot name="description">
            Estado físico y velocidad negociada de los puertos Ethernet
        </x-slot>

        <div wire:poll.10s="refreshInterfaces">

            @if ($error)
                <p class="mb-4 text-sm text-danger-600">
                    {{ $error }}
                </p>
            @endif

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6">

                @foreach ($interfaces as $interface)

                    @php
                        $enabled = $interface['enabled'];
                        $connected = $interface['connected'];
                        $speed = $interface['speed'];
                        $fullDuplex = $interface['full_duplex'];

                        $is100 = $connected
                            && $fullDuplex
                            && in_array($speed, ['100Mbps', '100M'], true);

                        $is1000 = $connected
                            && $fullDuplex
                            && in_array($speed, ['1Gbps', '1000Mbps', '1G'], true);

                        $green = $is100 ? '#22c55e' : '#64748b';
                        $orange = $is1000 ? '#f97316' : '#64748b';

                        $status = !$enabled
                            ? 'Disabled'
                            : ($connected ? 'Connected' : 'No link');
                    @endphp

                    <div
                        wire:key="port-{{ $interface['name'] }}"
                        @class([
                            'rounded-xl border border-gray-200 p-4 text-center dark:border-gray-700',
                            'opacity-50' => !$enabled,
                        ])
                    >

                        <div class="mb-3 text-sm font-semibold text-gray-950 dark:text-white">
                            {{ $interface['name'] }}
                        </div>

                        {{-- Puerto RJ45 --}}
                        <svg viewBox="0 0 120 105"
                             class="mx-auto h-28 w-28">

                            {{-- Exterior --}}
                            <rect x="8" y="7"
                                  width="104" height="92"
                                  rx="12"
                                  fill="#374151"/>

                            {{-- Conector metálico --}}
                            <path
                                d="M25 20 H95 V39 H101 V73
                                   H87 V86 H33 V73 H19 V39 H25 Z"
                                fill="#9ca3af"
                            />

                            {{-- Interior --}}
                            <path
                                d="M30 25 H90 V43 H96 V68
                                   H82 V79 H38 V68 H24 V43 H30 Z"
                                fill="#111827"
                            />

                            {{-- 8 contactos --}}
                            @for ($i = 0; $i < 8; $i++)
                                <rect
                                    x="{{ 36 + $i * 6 }}"
                                    y="29"
                                    width="3.5"
                                    height="20"
                                    rx="1"
                                    fill="#d1d5db"
                                />
                            @endfor

                            {{-- Pestaña inferior --}}
                            <rect x="41" y="66"
                                  width="38" height="7"
                                  rx="2"
                                  fill="#4b5563"/>

                            {{-- LED verde: 100MFD --}}
                            <rect x="12" y="13"
                                  width="13" height="9"
                                  rx="2"
                                  fill="{{ $green }}"
                                  opacity="{{ $is100 ? '1' : '.35' }}"
                            />

                            {{-- LED naranja: 1000MFD --}}
                            <rect x="95" y="13"
                                  width="13" height="9"
                                  rx="2"
                                  fill="{{ $orange }}"
                                  opacity="{{ $is1000 ? '1' : '.35' }}"
                            />

                        </svg>

                        <div class="mt-3">

                            <div @class([
                                'text-xs font-semibold',
                                'text-success-600 dark:text-success-400' => $connected,
                                'text-gray-400' => !$connected,
                            ])>
                                {{ $status }}
                            </div>

                            <div class="mt-1 text-xs font-medium text-gray-700 dark:text-gray-300">
                                {{ $connected ? ($speed ?? 'Unknown') : '—' }}
                            </div>

                            <div class="mt-1 text-xs text-gray-400">
                                @if ($connected)
                                    {{ $fullDuplex ? 'Full Duplex' : 'Half / Unknown Duplex' }}
                                @else
                                    Sin enlace
                                @endif
                            </div>

                            @if (!empty($interface['comment']))
                                <div class="mt-3 truncate border-t border-gray-200 pt-2 text-xs text-gray-500 dark:border-gray-700"
                                     title="{{ $interface['comment'] }}">
                                    {{ $interface['comment'] }}
                                </div>
                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

            <div class="mt-5 flex flex-wrap items-center gap-5 border-t border-gray-200 pt-4 text-xs text-gray-500 dark:border-gray-700">

                <span>🟢 100MFD</span>
                <span>🟠 1000MFD</span>
                <span>⚪ Sin enlace / Sin negociación</span>

                <span class="ml-auto">
                    {{ count($interfaces) }} interfaces · Actualización 10s
                </span>

            </div>

        </div>

    </x-filament::section>

</x-filament-widgets::widget>