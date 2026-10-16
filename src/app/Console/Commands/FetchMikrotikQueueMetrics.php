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
    protected $description = 'Obtiene los bytes acumulados de Queue Tree en MikroTik y reinicia contadores';

    public function handle(MikrotikService $mikrotikService): int
    {
        try {
            // 1. Obtenemos las colas (con sus bytes actuales)
            $queues = $mikrotikService->getQueueTrees();

            if (empty($queues)) {
                $this->warn('No se encontraron Queue Trees en el MikroTik.');
                return Command::SUCCESS;
            }

            $now = now();
            $records = [];

            foreach ($queues as $queue) {
                $name = $queue['name'] ?? null;
                
                // RouterOS devuelve los bytes como string numérico (ej. "536870916000")
                $bytes = isset($queue['bytes']) ? (int) $queue['bytes'] : 0;

                if (!$name) {
                    continue;
                }

                $records[] = [
                    'queue_name'     => $name,
                    'consumption_bytes' => $bytes, // Guardamos la verdad absoluta en bytes
                    'recorded_at'    => $now,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }

            if (!empty($records)) {
                ConsumptionLog::insert($records);
                $this->info("Se registraron " . count($records) . " colas correctamente en bytes.");
            }

            // 2. Limpiamos los contadores en MikroTik para el siguiente ciclo
            $mikrotikService->resetQueueTrees();

        } catch (Throwable $e) {
            Log::error("Error consultando MikroTik Queue Trees: " . $e->getMessage());
            $this->error("Falló la recolección: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}