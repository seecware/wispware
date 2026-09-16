<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Livewire\AgregarFiltroMac;

/*
|--------------------------------------------------------------------------
| Ruta Pública con lectura ARP para WISP
|--------------------------------------------------------------------------
*/
Route::get('/', function (Request $request) {
    $clientIp = $request->ip();
    $userAgent = $request->userAgent();
    $clientMac = 'No encontrada';

    if (file_exists('/proc/net/arp')) {
        $arpTable = file('/proc/net/arp');
        foreach ($arpTable as $line) {
            $parts = preg_split('/\s+/', trim($line));
            if (isset($parts[0], $parts[3]) && $parts[0] === $clientIp) {
                if ($parts[3] !== '00:00:00:00:00:00') {
                    $clientMac = strtoupper($parts[3]);
                }
                break;
            }
        }
    }

    return view('welcome', compact('clientIp', 'userAgent', 'clientMac'));
});

/*
|--------------------------------------------------------------------------
| Rutas Oficiales Autenticadas (Breeze + Livewire)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Perfil: Vista Blade nativa de Breeze Livewire
    Route::view('/profile', 'profile')->name('profile');

    // Módulos MikroTik
    Route::get('/filtro-mac', AgregarFiltroMac::class)->name('filtro-mac');
});

require __DIR__.'/auth.php';