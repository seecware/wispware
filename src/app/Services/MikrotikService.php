<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;
use Exception;

use App\Models\MikrotikSetting;

Class MikrotikService {
    protected ?Client $client = null;

public function connect(): self
{
    if (!$this->client) {

        $setting = MikrotikSetting::first();

        $this->client = new Client([
            'host' => $setting?->host
                ?? config('services.mikrotik.host', env('MIKROTIK_HOST')),

            'user' => $setting?->user
                ?? config('services.mikrotik.user', env('MIKROTIK_USER')),

            'pass' => config(
                'services.mikrotik.pass',
                env('MIKROTIK_PASS')
            ),

            'port' => (int) (
                $setting?->port
                ?? config(
                    'services.mikrotik.port',
                    env('MIKROTIK_PORT', 8728)
                )
            ),
        ]);
    }

    return $this;
}

    public function getSystemResource(): array
    {
        $this->connect();
        
        $query = new Query('/system/resource/print');
        return $this->client->query($query)->read();
    }

    public function getDhcpLeases(): array
    {
        $this->connect();

        $query = new Query('/ip/dhcp-server/lease/print');
        return $this->client->query($query)->read();
    }

public function getBridgeFilters(): array
{
    $this->connect();

    $query = new Query('/interface/bridge/filter/print');
    
    return $this->client->query($query)->read();
}

/**
 * Agrega una regla en Bridge Filter usando una MAC y un nombre/comentario personalizado
 */
public function addBridgeFilterMark(string $mac, string $comment, string $packetMark): array
{
    $this->connect();

    // 1. Limpiamos espacios y la pasamos a minúsculas
    $macLimpia = strtolower(trim($mac));

    // 2. Si venía con guiones (AA-BB-CC), los cambiamos por dos puntos (aa:bb:cc)
    $macLimpia = str_replace('-', ':', $macLimpia);

    // 3. Le agregamos la máscara de MAC /FF:FF:FF:FF:FF:FF que exige MikroTik en Bridge Filter
    if (!str_contains($macLimpia, '/')) {
        $macLimpia .= '/FF:FF:FF:FF:FF:FF';
    }

    $query = (new Query('/interface/bridge/filter/add'))
        ->equal('chain', 'forward')
        ->equal('dst-mac-address', $macLimpia)
        ->equal('action', 'mark-packet')
        ->equal('new-packet-mark', $packetMark)
        ->equal('passthrough', 'yes')
        ->equal('comment', trim($comment));

    return $this->client->query($query)->read();
}

public function existsMacInBridgeFilter(string $mac): bool
{
    $this->connect();

    // Normaliza la MAC al formato con máscara que usa RouterOS en bridge filter
    $macLimpia = strtolower(trim($mac));
    $macLimpia = str_replace('-', ':', $macLimpia);

    if (!str_contains($macLimpia, '/')) {
        $macLimpia .= '/FF:FF:FF:FF:FF:FF';
    }

    $query = (new Query('/interface/bridge/filter/print'))
        ->where('dst-mac-address', strtoupper($macLimpia));

    $result = $this->client->query($query)->read();

    return !empty($result);
}

public function getIpNeighbors(): array
{
    $this->connect();
    $query = new Query('/ip/neighbor/print');
    return $this->client->query($query)->read();
}

}