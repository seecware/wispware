<x-filament-widgets::widget>
    <x-filament::section>
        <div
            x-data
            x-init="$nextTick(() => {
                const canvas = $el.querySelector('canvas')
                if (canvas) {
                    canvas.style.height = '600px'
                    canvas.height = 600
                }
            })"
        >
            {{ $this->chart }}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>