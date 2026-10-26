
<x-filament-widgets::widget>
    <x-filament::section>

<x-slot name="heading">
    <div style="display: flex; align-items: center; gap: 10px;">
        <img
            src="{{ asset('img/mt.png') }}"
            alt="MikroTik"
            style="width: 24px; height: 24px; object-fit: contain;"
        >

        <span>MikroTik Interface Status</span>
    </div>
</x-slot>

        <x-slot name="description">
            Ethernet Ports Physical state and negotiated speed 
        </x-slot>

        <style>
            .mikrotik-ports {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(115px, 1fr));
                gap: 10px;
                width: 100%;
            }

            .mikrotik-port {
                min-width: 0;
                padding: 10px 6px;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                background: #ffffff;
                text-align: center;
                transition: border-color 0.2s;
            }

            .mikrotik-port:hover {
                border-color: #fb923c;
            }

            .mikrotik-port.disabled {
                opacity: 0.45;
            }

            .mikrotik-port-name {
                font-size: 12px;
                font-weight: 600;
                color: #111827;
                margin-bottom: 6px;
            }

            .mikrotik-port svg {
                display: block;
                width: 64px !important;
                height: 56px !important;
                max-width: 64px !important;
                min-width: 64px !important;
                margin: 0 auto;
            }

            .mikrotik-port-status {
                font-size: 11px;
                font-weight: 600;
                margin-top: 6px;
            }

            .mikrotik-port-speed {
                font-size: 11px;
                color: #374151;
                margin-top: 2px;
            }

            .mikrotik-port-duplex {
                font-size: 10px;
                color: #9ca3af;
                margin-top: 2px;
            }

            .mikrotik-port-comment {
                font-size: 10px;
                color: #6b7280;
                margin-top: 7px;
                padding-top: 6px;
                border-top: 1px solid #e5e7eb;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .mikrotik-legend {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                margin-top: 16px;
                padding-top: 12px;
                border-top: 1px solid #e5e7eb;
                font-size: 11px;
                color: #6b7280;
            }

            .mikrotik-legend-item {
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .mikrotik-legend-dot {
                display: inline-block;
                width: 9px;
                height: 9px;
                border-radius: 50%;
            }

            .dark .mikrotik-port {
                background: #111827;
                border-color: #374151;
            }

            .dark .mikrotik-port-name {
                color: #f9fafb;
            }

            .dark .mikrotik-port-speed {
                color: #d1d5db;
            }

            .dark .mikrotik-port-comment,
            .dark .mikrotik-legend {
                border-color: #374151;
            }
        </style>

        <div wire:poll.10s="refreshInterfaces">

            @if ($error)
                <div style="
                    padding: 10px;
                    margin-bottom: 12px;
                    background: #fef2f2;
                    color: #b91c1c;
                    border-radius: 8px;
                    font-size: 12px;
                ">
                    {{ $error }}
                </div>
            @endif

            <div class="mikrotik-ports">

                @forelse ($interfaces as $interface)

                    @php
                        $enabled = $interface['enabled'];
                        $connected = $interface['connected'];
                        $speed = $interface['speed'];
                        $fullDuplex = $interface['full_duplex'];

                        $is100 = $connected
                            && $fullDuplex
                            && in_array($speed, [
                                '100Mbps',
                                '100M'
                            ], true);

                        $is1000 = $connected
                            && $fullDuplex
                            && in_array($speed, [
                                '1Gbps',
                                '1000Mbps',
                                '1G'
                            ], true);

                        $green = $is100 ? '#22c55e' : '#64748b';
                        $orange = $is1000 ? '#f97316' : '#64748b';

                        $status = !$enabled
                            ? 'Disabled'
                            : ($connected ? 'Connected' : 'No link');
                    @endphp

                    <div
                        wire:key="port-{{ $interface['name'] }}"
                        class="mikrotik-port {{ !$enabled ? 'disabled' : '' }}"
                    >

                        <div class="mikrotik-port-name">
                            {{ $interface['name'] }}
                        </div>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 120 105"
                            width="64"
                            height="56"
                            role="img"
                            aria-label="Puerto {{ $interface['name'] }}: {{ $status }}"
                        >

                            {{-- Marco exterior --}}
                            <rect
                                x="8" y="7"
                                width="104"
                                height="92"
                                rx="12"
                                fill="#374151"
                            />

                            {{-- Contorno metálico --}}
                            <path
                                d="M25 20 H95 V39 H101 V73
                                   H87 V86 H33 V73 H19 V39 H25 Z"
                                fill="#9ca3af"
                            />

                            {{-- Interior del conector RJ45 --}}
                            <path
                                d="M30 25 H90 V43 H96 V68
                                   H82 V79 H38 V68 H24 V43 H30 Z"
                                fill="#111827"
                            />

                            {{-- Ocho contactos --}}
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
                            <rect
                                x="41" y="66"
                                width="38"
                                height="7"
                                rx="2"
                                fill="#4b5563"
                            />

                            {{-- LED verde 100MFD --}}
                            <rect
                                x="12" y="13"
                                width="13"
                                height="9"
                                rx="2"
                                fill="{{ $green }}"
                                opacity="{{ $is100 ? '1' : '.35' }}"
                            />

                            {{-- LED naranja 1000MFD --}}
                            <rect
                                x="95" y="13"
                                width="13"
                                height="9"
                                rx="2"
                                fill="{{ $orange }}"
                                opacity="{{ $is1000 ? '1' : '.35' }}"
                            />

                        </svg>

                        <div
                            class="mikrotik-port-status"
                            style="color: {{ $connected ? '#16a34a' : '#9ca3af' }};"
                        >
                            {{ $status }}
                        </div>

                        <div class="mikrotik-port-speed">
                            {{ $connected ? ($speed ?? 'Unknown') : '—' }}
                        </div>

                        <div class="mikrotik-port-duplex">
                            @if ($connected)
                                {{ $fullDuplex ? 'Full Duplex' : 'Half / Unknown Duplex' }}
                            @else
                                Sin enlace
                            @endif
                        </div>

                        @if (!empty($interface['comment']))
                            <div
                                class="mikrotik-port-comment"
                                title="{{ $interface['comment'] }}"
                            >
                                {{ $interface['comment'] }}
                            </div>
                        @endif

                    </div>

                @empty

                    <div style="
                        grid-column: 1 / -1;
                        padding: 24px;
                        text-align: center;
                        color: #9ca3af;
                        font-size: 13px;
                    ">
                        No se encontraron interfaces Ethernet.
                    </div>

                @endforelse

            </div>

            {{-- Leyenda --}}
            <div class="mikrotik-legend">

                <span class="mikrotik-legend-item">
                    <span
                        class="mikrotik-legend-dot"
                        style="background:#22c55e;"
                    ></span>
                    100MFD
                </span>

                <span class="mikrotik-legend-item">
                    <span
                        class="mikrotik-legend-dot"
                        style="background:#f97316;"
                    ></span>
                    1000MFD
                </span>

                <span class="mikrotik-legend-item">
                    <span
                        class="mikrotik-legend-dot"
                        style="background:#94a3b8;"
                    ></span>
                    Sin enlace
                </span>

                <span style="margin-left:auto;">
                    {{ count($interfaces) }} interfaces · Actualización 10s
                </span>

            </div>

        </div>

    </x-filament::section>
</x-filament-widgets::widget>
