<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ConsumptionLog;
use Illuminate\Support\Facades\Auth;
use Filament\Support\RawJs;

// ...

class ConsumptionLogChart extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Su consumo de datos:';
    public float $intervalConsumption = 0;
    public ?string $filter = '1';

    protected function getData(): array
    {
        $queueName = Auth::user()->queue_name;
        if (! $queueName) {
            return ['datasets' => [], 'labels' => []];
        }

        $logs = ConsumptionLog::query()
            ->where('queue_name', $queueName)
            ->whereDate('recorded_at', '>=', now()->subDays((int) $this->filter))
            ->orderBy('recorded_at', 'asc')
            ->get();

        // 1. Convertimos los bytes iniciales y finales a Megabytes (1 MB = 1024 * 1024 bytes)
        $initialBytes = $logs->first()?->consumption_bytes ?? 0;
        $finalBytes = $logs->last()?->consumption_bytes ?? 0;

        $initialMb = $initialBytes / 1048576;
        $finalMb = $finalBytes / 1048576;

        $this->intervalConsumption = $finalMb - $initialMb;

        // 2. Convertimos la colección de bytes a MB para la gráfica
        $mbValues = $logs->map(function ($log) {
            return round($log->consumption_bytes / 1048576, 2);
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Consumo (MegaBytes)',
                    'data' => $mbValues,
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'pointRadius' => 0,
                    'pointHoverRadius' => 4,
                ],
            ],
            'labels' => $logs->map(function ($log) {
                return $log->recorded_at
                    ->setTimezone('America/Mexico_City')
                    ->format('d/m H:i');
            })->toArray(),
        ];
    }

    // ...

    public function getDescription(): ?string
    {
        return '📈 ' . number_format($this->intervalConsumption, 2) . ' MB';
    }
}