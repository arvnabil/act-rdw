<?php

namespace Modules\WhatsApp\Filament\Pages;

use Modules\WhatsApp\Models\WhatsAppSetting;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class ManageWhatsAppBubble extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'WhatsApp Bubble';
    protected static ?string $title = 'Manage WhatsApp Bubble';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected string $view = 'whatsapp::filament.pages.manage-whats-app-bubble';

    public static function canAccess(): bool
    {
        return true;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = WhatsAppSetting::getInstance();
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Core Settings')
                    ->description('Basic WhatsApp configuration')
                    ->schema([
                        Toggle::make('is_enabled')
                            ->label('Enable Bubble')
                            ->helperText('Turn the WhatsApp bubble on or off on the website.')
                            ->default(true),
                        TextInput::make('phone')
                            ->label('WhatsApp Number')
                            ->placeholder('628123456789')
                            ->required()
                            ->helperText('Use international format without + or spaces (e.g., 62812...)'),
                        Textarea::make('message')
                            ->label('Default Message')
                            ->placeholder('Halo ACTiV, saya ingin konsultasi...')
                            ->rows(3),
                        TextInput::make('tooltip')
                            ->label('Tooltip Text')
                            ->placeholder('Chat WhatsApp Kami'),
                    ])->columns(2),

                Section::make('Appearance & Animation')
                    ->description('Customize how the bubble looks')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('position')
                                ->options([
                                    'bottom-right' => 'Bottom Right',
                                    'bottom-left' => 'Bottom Left',
                                ])
                                ->default('bottom-right'),
                            Toggle::make('show_online_badge')
                                ->label('Show Online Badge')
                                ->default(true),
                            Toggle::make('show_pulse_animation')
                                ->label('Show Pulse Animation')
                                ->default(true),
                            Toggle::make('open_in_new_tab')
                                ->label('Open In New Tab')
                                ->default(true),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('offset_bottom')
                                ->label('Offset Bottom')
                                ->default('24px'),
                            TextInput::make('offset_side')
                                ->label('Offset Side')
                                ->default('24px'),
                        ]),
                        FileUpload::make('icon')
                            ->label('Custom Icon')
                            ->image()
                            ->disk('public')
                            ->directory('whatsapp'),
                        ColorPicker::make('button_color')
                            ->label('Button Color')
                            ->default('#25D366'),
                    ]),

                Section::make('Visibility')
                    ->description('Select which pages should display the bubble')
                    ->schema([
                        CheckboxList::make('active_pages')
                            ->options([
                                'all' => 'All Pages',
                                'homepage' => 'Homepage Only',
                                'products' => 'Product Pages Only',
                            ])
                            ->default(['all']),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = WhatsAppSetting::getInstance();
        $settings->update($data);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
