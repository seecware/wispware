<?php

namespace App\Filament\Pages;

use App\Services\MikrotikService;
use Filament\Pages\Page;

class ActiveDevices extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    protected static string $view = 'filament.pages.active-devices';

    public array $devices = [];

    public function mount(): void
    {
        $this->devices = app(MikrotikService::class)->getIpNeighbors();
    }

    public function pingDevices(): void
{
    foreach ($this->devices as &$device) {
        $ip = $device['address'] ?? null;

        if (!$ip) {
            continue;
        }

        $device['ping'] = $this->ping($ip);
    }
}

private function ping(string $ip): ?float
{
    $output = [];
    $result = 0;

    exec(
        "ping -c 1 -W 1 " . escapeshellarg($ip),
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
}