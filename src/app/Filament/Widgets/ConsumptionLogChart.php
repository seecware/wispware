<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ConsumptionLog;
use Illuminate\Support\Facades\Auth;
use Filament\Support\RawJs;

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

        // 1. Convertimos el primer y último registro de bytes a GB
        $initialBytes = $logs->first()?->consumption_bytes ?? 0;
        $finalBytes = $logs->last()?->consumption_bytes ?? 0;

        $initialGb = $initialBytes / 1073741824;
        $finalGb = $finalBytes / 1073741824;

        $this->intervalConsumption = $finalGb - $initialGb;

        // 2. Mapeamos la colección de logs convirtiendo cada registro de bytes a GB
        $gbValues = $logs->map(function ($log) {
            return round($log->consumption_bytes / 1073741824, 2);
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Consumo (GigaBytes)',
                    'data' => $gbValues, // Le pasamos el arreglo ya convertido a GB
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

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * Inyecta la configuración visual avanzada a Chart.js
     */
    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 300, // Mantiene la escala de 300 GB exacta
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            },
            elements: {
                line: {
                    borderColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;

                        if (!chartArea) {
                            return '#10B981';
                        }

                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        
                        gradient.addColorStop(0.0, '#10B981'); // 0 GB -> Verde
                        gradient.addColorStop(0.25, '#3B82F6'); // ~75 GB -> Azul
                        gradient.addColorStop(0.50, '#FBBF24'); // ~150 GB -> Amarillo
                        gradient.addColorStop(0.75, '#F97316'); // ~225 GB -> Naranja
                        gradient.addColorStop(1.0, '#EF4444');  // 300 GB -> Rojo (Tope)

                        return gradient;
                    },
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;

                        if (!chartArea) {
                            return 'rgba(16, 185, 129, 0.2)';
                        }

                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0.0, 'rgba(16, 185, 129, 0.15)');
                        gradient.addColorStop(0.25, 'rgba(59, 130, 246, 0.15)');
                        gradient.addColorStop(0.50, 'rgba(251, 191, 36, 0.20)');
                        gradient.addColorStop(0.75, 'rgba(249, 115, 22, 0.25)');
                        gradient.addColorStop(1.0, 'rgba(239, 68, 68, 0.35)');

                        return gradient;
                    }
                }
            }
        }
        JS);
    }

    protected function getFilters(): array
    {
        return [
            '1' => 'Today',
            '2' => 'Last 2 days',
            '3' => 'Last 3 days',
            '7' => 'Last week'
        ];
    }

    public function getDescription(): ?string
    {
        return '📈 ' . number_format($this->intervalConsumption, 2) . ' GB';
    }
}