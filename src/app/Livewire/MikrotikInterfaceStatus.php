<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

use App\Services\MikrotikService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;
use Throwable;

class MikrotikInterfaceStatus extends Widget
{
    protected static string $view = 'livewire.mikrotik-interface-status';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -100;

    public array $interfaces = [];

    public ?string $error = null;

    public function mount(): void
    {
        $this->refreshInterfaces();
    }

    public function refreshInterfaces(): void
    {
        try {
            $this->interfaces = app(MikrotikService::class)
                ->getInterfaces();

            $this->error = null;

        } catch (Throwable $e) {
            Log::error('MikroTik interface status', [
                'message' => $e->getMessage(),
            ]);

            $this->error = 'No se pudo consultar el MikroTik.';
        }
    }
}
