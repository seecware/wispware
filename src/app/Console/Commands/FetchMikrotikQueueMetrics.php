<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikService;
use App\Models\ConsumptionLog;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchMikrotikQueueMetrics extends Command
{
    protected $signature = 'mikrotik:fetch-queues';

    protected $description = 'Obtiene los bytes de Queue Tree, identifica el ISP y reinicia contadores';

    public function handle(MikrotikService $mikrotikService): int
    {
        try {
            // 1. Obtener MAC del ISP (null si hay 0 o varias)
            $ispMacOrigin = $mikrotikService->getIspMacOrigin();

            // 2. Obtener Queue Trees
            $queues = $mikrotikService->getQueueTrees();

            if (empty($queues)) {
                $this->warn('No se encontraron Queue Trees.');
                return Command::SUCCESS;
            }

            $now = now();
            $records = [];

            foreach ($queues as $queue) {
                $name = $queue['name'] ?? null;

                $bytes = isset($queue['bytes'])
                    ? (int) $queue['bytes']
                    : 0;

                if (!$name) {
                    continue;
                }

                $records[] = [
                    'queue_name' => $name,
                    'consumption_bytes' => $bytes,
                    'recorded_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'isp_mac_origin' => $ispMacOrigin,
                ];
            }

            // 3. Guardar registros
            if (!empty($records)) {
                ConsumptionLog::insert($records);

                $this->info(
                    'Se registraron ' . count($records) .
                    ' colas. ISP MAC: ' . ($ispMacOrigin ?? 'NULL')
                );

                // 4. Reiniciar contadores
                $mikrotikService->resetQueueTrees();
            }

        } catch (Throwable $e) {
            Log::error(
                'Error consultando MikroTik Queue Trees: ' .
                $e->getMessage()
            );

            $this->error(
                'Falló la recolección: ' . $e->getMessage()
            );

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
