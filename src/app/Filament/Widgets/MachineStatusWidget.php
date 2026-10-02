<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ConsumptionLog;

class MachineStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Consumo Acumulado Total (GB)';

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
        $userTimezone = 'America/Mexico_City';

        $colors = [
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
            '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1',
        ];

        $query = ConsumptionLog::query();

        if ($this->filter === 'all_without_download') {
            $query->where('queue_name', '!=', 'Download');
        } elseif (! in_array($this->filter, ['all', 'all_without_download'])) {
            $query->where('queue_name', $this->filter);
        }

        $allLogs = $query->orderBy('recorded_at')->get();

        if ($allLogs->isEmpty()) {
            return ['datasets' => []];
        }

        $groupedLogs = $allLogs->groupBy('queue_name');
        $datasets = [];
        $colorIndex = 0;

        foreach ($groupedLogs as $queueName => $queueLogs) {
            $color = $colors[$colorIndex % count($colors)];
            $isDownload = ($queueName === 'Download');

            // Enviamos coordenadas {x: fecha_iso, y: valor}
            $dataPoints = $queueLogs->map(function ($log) use ($userTimezone) {
                return [
                    'x' => $log->recorded_at->clone()->setTimezone($userTimezone)->toIso8601String(),
                    'y' => (float) $log->consumption_gb,
                ];
            })->values()->toArray();

            $datasets[] = [
                'label' => $queueName,
                'data' => $dataPoints,
                'borderColor' => $color,
                'backgroundColor' => $color,
                'borderWidth' => 1.5,
                'pointRadius' => 0,
                'pointHoverRadius' => 0,
                'fill' => false,
                'tension' => 0.1,
                'spanGaps' => true, // Conecta lecturas existentes; la escala X mantiene el espacio proporcional de tiempo
                'hidden' => ($this->filter === 'all' && $isDownload),
            ];

            $colorIndex++;
        }

        return [
            'datasets' => $datasets,
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
                    'type' => 'time', // Escala de tiempo nativa de Chart.js
                    'time' => [
                        'tooltipFormat' => 'DD/MM HH:mm',
                        'displayFormats' => [
                            'hour' => 'DD/MM HH:mm',
                            'day' => 'DD/MM',
                        ],
                    ],
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