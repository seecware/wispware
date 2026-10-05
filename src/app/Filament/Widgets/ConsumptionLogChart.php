<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ConsumptionLog;
use Illuminate\Support\Facades\Auth;

class ConsumptionLogChart extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Su consumo de datos:';
    public float $intervalConsumption = 0;
    public ?string $filter='2';

    protected function getData(): array
    {
        $queueName = Auth::user()->queue_name;
        if (! $queueName) {
        return ['datasets' => [], 'labels' => []];
        }

        $logs = ConsumptionLog::query()
            ->where('queue_name',$queueName)
            ->whereDate('recorded_at', '>=', now()->subDays((int) $this->filter))
            ->orderBy('recorded_at','asc')
            ->get();


        $initial = $logs->first()?->consumption_gb ?? 0;
        $final = $logs->last()?->consumption_gb ?? 0;
        $this->intervalConsumption = $final-$initial;


        $labels = $logs->map(fn ($log)=> $log->recorded_at->format('Y-m-d H:i'))->toArray();
        $data = $logs->pluck('consumption_gb')->toArray();
        return [
            'datasets' => [
                [
                    'label' => 'Consumo (GigaBytes)',
                    'data' => $data,
                    'borderColor'=>'#10B981',
                ],
            ],
            'labels' => $logs->map(function ($log){
                return $log->recorded_at
                    //->setTimezone('America/Mexico_City')
                    ->format('d/m H:i');
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters():array
    {
        return [
            '2' => 'Last two days',
            '7' => 'This week',
            '30' => 'This month',   // Siguiente funcionalidad: Agregar filtros por mes, ene, feb, mar, etc.
        ];
    }

    public function getDescription(): ?string
    {
        return '📈' . number_format($this->intervalConsumption,2) . 'GB';
    }

}
