<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ConsumptionLog;

class MachineStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Consumo por Intervalo (GB)';

    // Ocupa todo el ancho del grid
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        $queues = ConsumptionLog::query()
            ->distinct()
            ->orderBy('queue_name')
            ->pluck('queue_name', 'queue_name')
            ->toArray();

        return [
            'all' => 'Todos (Incluye Download)',
            'all_without_download' => 'Todos (Sin Download)',
            ...$queues,
        ];
    }

    protected function getData(): array
    {
        $colors = [
            '#3b82f6', // Azul
            '#ef4444', // Rojo
            '#10b981', // Verde
            '#f59e0b', // Naranja/Amarillo
            '#8b5cf6', // Morado
            '#ec4899', // Rosa
            '#06b6d4', // Cían
            '#84cc16', // Lima
            '#f97316', // Naranja fuerte
            '#6366f1', // Índigo
        ];

        // =========================================================
        // UN SOLO CLIENTE
        // =========================================================

        if (! in_array($this->filter, ['all', 'all_without_download'])) {

            $logs = ConsumptionLog::query()
                ->where('queue_name', $this->filter)
                ->where('recorded_at', '>=', now()->subHours(24))
                ->orderBy('recorded_at')
                ->get();

            $diffData = [];
            $previousLog = null;

            foreach ($logs as $log) {
                if ($previousLog === null) {
                    $diffData[] = 0;
                } else {
                    $diffData[] = $this->calculateNormalizedDiff($previousLog, $log);
                }

                $previousLog = $log;
            }

            return [
                'datasets' => [
                    [
                        'label' => "Consumo por intervalo GB ({$this->filter})",
                        'data' => $diffData,
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'borderWidth' => 1.5,
                        'pointRadius' => 0,
                        'pointHoverRadius' => 0,
                        'fill' => true,
                        'tension' => 0.2,
                    ],
                ],

                'labels' => $logs
                    ->map(fn ($log) => $log->recorded_at->format('H:i'))
                    ->toArray(),
            ];
        }

        // =========================================================
        // TODOS LOS CLIENTES
        // =========================================================

        $query = ConsumptionLog::query()
            ->where('recorded_at', '>=', now()->subHours(24));

        if ($this->filter === 'all_without_download') {
            $query->where('queue_name', '!=', 'Download');
        }

        $logs = $query->orderBy('recorded_at')
            ->get()
            ->groupBy('queue_name');

        $datasets = [];
        $colorIndex = 0;

        foreach ($logs as $queueName => $queueLogs) {

            $color = $colors[$colorIndex % count($colors)];
            $isDownload = ($queueName === 'Download');

            $diffData = [];
            $previousLog = null;

            foreach ($queueLogs as $log) {
                if ($previousLog === null) {
                    $diffData[] = 0;
                } else {
                    $diffData[] = $this->calculateNormalizedDiff($previousLog, $log);
                }

                $previousLog = $log;
            }

            $datasets[] = [
                'label' => $queueName,
                'data' => $diffData,
                'borderColor' => $color,
                'backgroundColor' => $color,
                'borderWidth' => 1.5,
                'pointRadius' => 0,
                'pointHoverRadius' => 0,
                'fill' => false,
                'tension' => 0.2,
                'hidden' => ($this->filter === 'all' && $isDownload),
            ];

            $colorIndex++;
        }

        $labelsQuery = ConsumptionLog::query()
            ->where('recorded_at', '>=', now()->subHours(24));

        if ($this->filter === 'all_without_download') {
            $labelsQuery->where('queue_name', '!=', 'Download');
        }

        $labels = $labelsQuery->orderBy('recorded_at')
            ->pluck('recorded_at')
            ->map(fn ($date) => $date->format('H:i'))
            ->unique()
            ->values()
            ->toArray();

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    /**
     * Calcula la diferencia normalizada en base al tiempo real transcurrido entre muestras.
     */
    private function calculateNormalizedDiff(ConsumptionLog $previousLog, ConsumptionLog $currentLog): float
    {
        $currentValue = (float) $currentLog->consumption_gb;
        $previousValue = (float) $previousLog->consumption_gb;

        // 1. Manejo de reinicio en MikroTik o datos corruptos
        if ($currentValue < $previousValue) {
            return 0;
        }

        $rawDiff = $currentValue - $previousValue;

        // 2. Tiempo transcurrido en minutos entre ambos registros
        $minutesPassed = $previousLog->recorded_at->diffInMinutes($currentLog->recorded_at);

        // Si la muestra es instantánea (menos de 1 min), retornamos la diferencia directa
        if ($minutesPassed <= 0) {
            return round($rawDiff, 4);
        }

        // 3. CIRCUIT BREAKER (Apagones largos o mantenimientos > 20 minutos)
        // Si la diferencia de tiempo es muy grande, descartamos el pico y reajustamos en 0.
        if ($minutesPassed > 20) {
            return 0;
        }

        // 4. NORMALIZACIÓN AL INTERVALO ESTÁNDAR (5 MINUTOS)
        // Si el scheduler se retrasó un poco (ej. pasaron 10 min en lugar de 5),
        // dividimos la diferencia entre los minutos reales y normalizamos a una ventana de 5 min.
        $diffPerMinute = $rawDiff / $minutesPassed;
        $normalizedDiff = $diffPerMinute * 5;

        return round($normalizedDiff, 4);
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'boxWidth' => 8,
                        'boxHeight' => 8,
                        'padding' => 20,
                        'font' => [
                            'family' => 'Inter, sans-serif',
                            'size' => 12,
                            'weight' => '500',
                        ],
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => true,
                        'drawOnChartArea' => true,
                        'drawTicks' => true,
                        'color' => 'rgba(156, 163, 175, 0.15)',
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => true,
                        'color' => 'rgba(156, 163, 175, 0.15)',
                    ],
                ],
            ],
            'maintainAspectRatio' => true,
        ];
    }
}