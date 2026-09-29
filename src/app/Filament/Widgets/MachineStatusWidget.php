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

            // Cálculo diferencial
            $diffData = [];
            $previousValue = null;

            foreach ($logs as $log) {
                $currentValue = (float) $log->consumption_gb;

                if ($previousValue === null) {
                    $diffData[] = 0;
                } else {
                    $diff = $currentValue - $previousValue;
                    $diffData[] = $diff < 0 ? 0 : round($diff, 4);
                }

                $previousValue = $currentValue;
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

            // Cálculo diferencial por cliente
            $diffData = [];
            $previousValue = null;

            foreach ($queueLogs as $log) {
                $currentValue = (float) $log->consumption_gb;

                if ($previousValue === null) {
                    $diffData[] = 0;
                } else {
                    $diff = $currentValue - $previousValue;
                    $diffData[] = $diff < 0 ? 0 : round($diff, 4);
                }

                $previousValue = $currentValue;
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
                        'usePointStyle' => true,        // <--- Cambia el rectángulo feo por un círculo
                        'pointStyle' => 'circle',       // <--- Forma redonda limpia
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