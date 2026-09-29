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
    protected $description = 'Obtiene los Gigas acumulados del comentario en Queue Tree de MikroTik';

    public function handle(MikrotikService $mikrotikService): int
    {
        try {
            $queues = $mikrotikService->getQueueTrees();

            if (empty($queues)) {
                $this->warn('No se encontraron Queue Trees en el MikroTik.');
                return Command::SUCCESS;
            }

            $now = now();
            $records = [];

            foreach ($queues as $queue) {
                $name = $queue['name'] ?? null;
                $comment = $queue['comment'] ?? null;

                if (!$name) {
                    continue;
                }

                // Extraemos el valor del comentario (ej. "20.018687154")
                $gbAccumulated = (float) trim((string) $comment);

                $records[] = [
                    'queue_name'     => $name,
                    'consumption_gb' => round($gbAccumulated, 4),
                    'recorded_at'    => $now,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }

            if (!empty($records)) {
                ConsumptionLog::insert($records);
                $this->info("Se registraron " . count($records) . " colas correctamente.");
            }

        } catch (Throwable $e) {
            Log::error("Error consultando MikroTik Queue Trees: " . $e->getMessage());
            $this->error("Falló la recolección: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}