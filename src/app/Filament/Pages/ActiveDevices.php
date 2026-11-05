<?php

namespace App\Filament\Pages;

use App\Services\MikrotikService;
use Filament\Pages\Page;

class ActiveDevices extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    protected static string $view = 'filament.pages.active-devices';

    protected static ?string $navigationLabel = 'Active Devices';

    protected static ?string $title = 'Network Devices';

    public string $sortBy = 'ping_desc';

    public array $devices = [];

    public function mount(): void
    {
        //$this->refreshDevices();
    }

    public function refreshDevices(): void
    {
        $this->devices = app(MikrotikService::class)
            ->getIpNeighbors();

        $this->pingDevices();
    }

    public function pingDevices(): void
    {
        foreach ($this->devices as &$device) {
            $ip = $device['address'] ?? null;

            $device['ping'] = $ip
                ? $this->ping($ip)
                : null;
        }

        unset($device);
    }

    public function getStatsProperty(): array
    {
        $total = count($this->devices);

        $online = collect($this->devices)
            ->filter(fn ($device) => ($device['ping'] ?? null) !== null)
            ->count();

        return [
            'total' => $total,
            'online' => $online,
            'offline' => $total - $online,
        ];
    }

    private function ping(string $ip): ?float
    {
        // Validar que se trate de una dirección IP.
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }

        $output = [];
        $result = 0;

        exec(
            'ping -n -c 1 -W 1 ' . escapeshellarg($ip),
            $output,
            $result
        );

        if ($result !== 0) {
            return null;
        }

        foreach ($output as $line) {
            if (preg_match('/time[=<]([0-9.]+)\s*ms/', $line, $matches)) {
                return (float) $matches[1];
            }
        }

        return null;
    }

    public function getSortedDevicesProperty(): array
{
    return collect($this->devices)
        ->sort(function ($a, $b) {
            $pingA = $a['ping'] ?? null;
            $pingB = $b['ping'] ?? null;

            // Los dispositivos sin respuesta van al principio.
            if ($pingA === null && $pingB !== null) return -1;
            if ($pingB === null && $pingA !== null) return 1;
            if ($pingA === null && $pingB === null) return 0;

            return $this->sortBy === 'ping_asc'
                ? $pingA <=> $pingB
                : $pingB <=> $pingA;
        })
        ->values()
        ->all();
}
}
