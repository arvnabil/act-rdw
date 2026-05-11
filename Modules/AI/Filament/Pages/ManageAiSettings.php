<?php

namespace Modules\AI\Filament\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Modules\Settings\Models\Setting;
use Filament\Notifications\Notification;

class ManageAiSettings extends Page implements \Filament\Forms\Contracts\HasForms
{
    use \Filament\Forms\Concerns\InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-sparkles';
    protected static string | \UnitEnum | null $navigationGroup = 'AI Management';
    protected static ?string $navigationLabel = 'AI Chatbot Settings';
    protected static ?string $title = 'AI Chatbot Settings';
    protected string $view = 'ai::filament.pages.manage-ai-settings';

    public ?array $data = [];

    public function mount()
    {
        $settings = Setting::whereIn('key', [
            'vion_is_active',
            'vion_welcome_message',
            'vion_starter_buttons',
        ])->pluck('value', 'key');

        $this->form->fill([
            'vion_is_active' => ($settings['vion_is_active'] ?? '1') === '1',
            'vion_welcome_message' => $settings['vion_welcome_message'] ?? 'Halo! Saya Vion, ICT Solutions Consultant Anda. Ada yang bisa saya bantu hari ini?',
            'vion_starter_buttons' => json_decode($settings['vion_starter_buttons'] ?? '[]', true),
        ]);
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Section::make('General Availability')
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('vion_is_active')
                            ->label('Enable Vion Assistant')
                            ->helperText('Turn this OFF to hide the chatbot bubble from the website.')
                            ->default(true),
                    ]),

                Section::make('Greetings & Welcome')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('vion_welcome_message')
                            ->label('Welcome Message')
                            ->helperText('This message is shown right after the user fills the lead form. Use [Nama] for personalization.')
                            ->rows(3)
                            ->required(),
                    ]),

                Section::make('Starter Buttons (Hybrid Support)')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('info')
                            ->content('If "Instant Response" is filled, Vion will answer immediately without using AI. If empty, it will use AI to handle the response.'),
                        \Filament\Forms\Components\Repeater::make('vion_starter_buttons')
                            ->label('Buttons')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('label')
                                    ->label('Button Label')
                                    ->placeholder('e.g. Solusi Meeting Room')
                                    ->required()
                                    ->columnSpan(1),
                                \Filament\Forms\Components\TextInput::make('message')
                                    ->label('Trigger Message (to AI)')
                                    ->placeholder('e.g. Saya tertarik solusi meeting')
                                    ->required()
                                    ->columnSpan(1),
                                \Filament\Forms\Components\Textarea::make('instant_response')
                                    ->label('Instant Response (Optional - Hardcoded)')
                                    ->placeholder('Leave empty to let AI handle the response...')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->grid(1)
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
                    ]),

                Section::make('AI Analysis & Trends (Draft FAQ)')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('analysis_status')
                            ->label('Status Analisa')
                            ->content('Sistem sedang mengumpulkan data percakapan. Saat tren pertanyaan mencapai 10+ diskusi serupa, rekomendasi FAQ baru akan muncul di sini sebagai draft.'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save()
    {
        $data = $this->form->getState();

        Setting::updateOrCreate(['key' => 'vion_is_active'], ['value' => $data['vion_is_active'] ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'vion_welcome_message'], ['value' => $data['vion_welcome_message']]);
        Setting::updateOrCreate(['key' => 'vion_starter_buttons'], ['value' => json_encode($data['vion_starter_buttons'])]);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }
}
