<x-filament-panels::page>
    <div wire:poll.10s="pingDevices">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

        @foreach ($devices as $device)

            <x-filament::section>
                
                <div class="space-y-3">

                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">
                            {{ $device['identity'] ?? 'Unknown Device' }}
                        </h2>

<x-filament::badge
                                :color="
        ($device['ping'] ?? null) === null
            ? 'danger'
            : (
                $device['ping'] <= 20
                    ? 'success'
                    : (
                        $device['ping'] <= 50
                            ? 'warning'
                            : 'danger'
                    )
            )
    "
                            >
                                {{ ($device['ping'] ?? null) !== null
                                    ? $device['ping'] . ' ms'
                                    : 'Timeout'
                                }}
                            </x-filament::badge>
                    </div>

                    <div class="space-y-1 text-sm">
                        <div>
                            <a href="http://{{ $device['address'] ?? 'N/A' }}" target="_blank" rel="noopener noreferrer" class="font-medium">IP: {{ $device['address'] ?? 'N/A' }}</a>
                        </div>

                        <div>
                            <span class="font-medium">MAC:</span>
                            {{ $device['mac-address'] ?? 'N/A' }}
                        </div>

                        <div>
                            <span class="font-medium">Version:</span>
                            {{ $device['version'] ?? 'N/A' }}
                        </div>

                        <div>
                            <span class="font-medium">Interface:</span>
                            {{ $device['interface'] ?? 'N/A' }}
                        </div>
                    </div>

                </div>

            </x-filament::section>

        @endforeach
</div>
    </div>

</x-filament-panels::page>