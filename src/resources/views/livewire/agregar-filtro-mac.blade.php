<div class="mt-6 pt-6 border-t border-slate-700">
    <h3 class="text-base font-bold text-sky-400 mb-4 flex items-center gap-2">
        ⚡ Agregar Regla a Bridge Filter
    </h3>

    @if ($mensaje)
        <div class="p-3 mb-4 text-sm text-emerald-300 bg-emerald-950/80 border border-emerald-500/30 rounded-md">
            {{ $mensaje }}
        </div>
    @endif

    <form wire:submit="guardarFiltro" class="space-y-4">
        <!-- Input: Nombre de la Regla (Comment) -->
        <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Nombre / Identificador de la regla:</label>
            <input 
                type="text" 
                wire:model="comment" 
                placeholder="Ej. Cliente_Juan_Perez" 
                class="w-full px-3 py-2 bg-slate-900 text-slate-100 placeholder-slate-500 border border-slate-700 rounded-md focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 text-sm font-mono @error('comment') border-rose-500 @enderror"
            >
            @error('comment') 
                <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Input: Dirección MAC -->
        <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Dirección MAC Detectada:</label>
            <input 
                type="text" 
                wire:model="mac" 
                readonly
                class="w-full px-3 py-2 bg-slate-950 text-yellow-400 border rounded-md focus:outline-none text-sm font-mono cursor-not-allowed select-none @if($existeEnMikrotik) border-rose-500/60 text-rose-400 @else border-slate-700 @endif"
            >
            @if ($existeEnMikrotik)
                <span class="text-xs text-rose-400 mt-1 block font-semibold flex items-center gap-1">
                    🚫 Esta MAC ya se encuentra registrada en las reglas de Bridge Filter.
                </span>
            @endif
        </div>

        <!-- Select: Packet Mark (Dinamico) -->
        <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Packet Mark (Marca de paquete):</label>
            <select 
                wire:model="packetMark"
                class="w-full px-3 py-2 bg-slate-900 text-sky-300 border border-slate-700 rounded-md focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 text-sm font-mono cursor-pointer"
            >
               <!-- <option value="trusted">trusted</option>
                <option value="untrusted">untrusted</option>
                <option value="infraestructure">infrastructure</option>
                -->
                
                <option value="devs">devs</option>
                
                <!--
                <option value="test">test</option>
                <option value="blocked">blocked</option>
                <option value="clients3">clients devices</option>
                -->
                <option value="clients2">clients2m</option>
               <!-- <option value="clients5">clients3m</option>
                <option value="clients7">clients4m</option>
                -->
            </select>
            @error('packetMark') 
                <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Botón submit -->
<button 
            type="submit" 
            wire:loading.attr="disabled"
            @disabled($existeEnMikrotik)
            class="w-full px-4 py-2.5 font-bold text-sm rounded-md transition-colors shadow-lg @if($existeEnMikrotik) bg-slate-800 text-slate-500 cursor-not-allowed border border-slate-700 @else text-slate-900 bg-sky-400 hover:bg-sky-300 cursor-pointer @endif"
        >
            <span wire:loading.remove>
                @if($existeEnMikrotik)
                    MAC Ya Filtrada
                @else
                    Crear Filtro en MikroTik
                @endif
            </span>
            <span wire:loading>Enviando comando a RouterOS...</span>
        </button>
    </form>
</div>