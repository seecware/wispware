<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\MikrotikService;

class AgregarFiltroMac extends Component
{
    public string $mac = '';
    public string $comment = '';
    public string $packetMark = 'clients2'; // Valor por defecto
    public ?string $mensaje = null;
    public bool $existeEnMikrotik = false;

    public function mount(string $mac = '', MikrotikService $mikrotik)
    {
        if ($mac !== 'No encontrada') {
            $this->mac = $mac;
            $this->existeEnMikrotik = $mikrotik->existsMacInBridgeFilter($this->mac);
        }
    }

    public function guardarFiltro(MikrotikService $mikrotik)
    {

    if ($this->existeEnMikrotik) {
            return;
        }

        $this->validate([
            'mac'        => 'required|string',
            'comment'    => 'required|string|min:3|max:50',
            'packetMark' => 'required|string',
        ]);

        // Pasa el packetMark seleccionado dinámicamente
        $response = $mikrotik->addBridgeFilterMark($this->mac, $this->comment, $this->packetMark);

        $this->reset('comment');
        $this->mensaje = 'Filtro agregado correctamente en el MikroTik.';
        $this->existeEnMikrotik = true;
    }

    public function render()
    {
        return view('livewire.agregar-filtro-mac');
    }
}