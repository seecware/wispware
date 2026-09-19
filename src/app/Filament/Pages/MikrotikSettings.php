<?php

namespace App\Filament\Pages;

use App\Models\MikrotikSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MikrotikSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-server';

    protected static ?string $navigationLabel = 'MikroTik';

    protected static ?string $title = 'Configuración de MikroTik';

    protected static string $view = 'filament.pages.mikrotik-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = MikrotikSetting::first();

        $this->form->fill([
            'host' => $setting?->host ?? '',
            'port' => $setting?->port ?? 8728,
            'user' => $setting?->user ?? '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('host')
                    ->label('Dirección IP / Host')
                    ->required(),

                Forms\Components\TextInput::make('port')
                    ->label('Puerto')
                    ->numeric()
                    ->default(8728)
                    ->required(),

                Forms\Components\TextInput::make('user')
                    ->label('Usuario')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->validate();

        MikrotikSetting::updateOrCreate(
            ['id' => 1],
            [
                'host' => $this->data['host'],
                'port' => $this->data['port'],
                'user' => $this->data['user'],
            ]
        );

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }
}