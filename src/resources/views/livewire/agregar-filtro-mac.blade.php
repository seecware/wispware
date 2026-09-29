<div class="mt-6 pt-6 border-t border-slate-700">
    <h3 class="text-base font-bold text-sky-400 mb-4 flex items-center gap-2">
       Sign up device
    </h3>

    @if ($mensaje)
        <div class="p-3 mb-4 text-sm text-emerald-300 bg-emerald-950/80 border border-emerald-500/30 rounded-md">
            {{ $mensaje }}
        </div>
    @endif

    <form wire:submit="guardarFiltro" class="space-y-4">
        <!-- Input: Nombre de la Regla (Comment) -->
        <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Name:</label>
            <input 
                type="text" 
                wire:model="comment" 
                placeholder="Your Name" 
                class="w-full px-3 py-2 bg-slate-900 text-slate-100 placeholder-slate-500 border border-slate-700 rounded-md focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 text-sm font-mono @error('comment') border-rose-500 @enderror"
            >
            @error('comment') 
                <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Input: Dirección MAC -->
<div>
    <label class="block text-xs font-semibold text-gray-600 bg-yellow-100 px-2 py-1 rounded mb-1">Detected MAC address:</label>
    <input
        type="text" 
        wire:model="mac"
        readonly
        class="block w-full text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-300 rounded px-2 py-1 mb-1 focus:outline-none"
    />

    @if ($existeEnMikrotik)
        <span class="text-xs text-rose-400 mt-1 block font-semibold flex items-center gap-1">
            This MAC ADDRESS is already up!
        </span>
    @endif
</div>

        <!-- Select: Packet Mark (Dinamico) -->
        <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Packet Mark:</label>
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
                    Already signup
                @else
                    Sign up device
                @endif
            </span>
            <span wire:loading>Sending data to server...</span>
        </button>
    </form>
</div>