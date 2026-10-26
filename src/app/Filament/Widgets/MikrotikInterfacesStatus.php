<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

use App\Services\MikrotikService;
use Illuminate\Support\Facades\Log;
use Throwable;

class MikrotikInterfacesStatus extends Widget
{
    protected static string $view = 'filament.widgets.mikrotik-interfaces-status';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

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
