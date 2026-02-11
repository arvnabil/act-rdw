<?php

namespace Modules\Events\Filament\Resources\EventResource\Schemas;

use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\Select::make('event_category_id')
                ->relationship('category', 'name')
                ->required(),
            Forms\Components\TextInput::make('title')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->disabled()
                ->maxLength(255),
            Forms\Components\RichEditor::make('description')
                ->required()
                ->columnSpanFull()
                ->fileAttachmentsAcceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'])
                ->fileAttachmentsMaxSize(2048)
                ->fileAttachmentsDisk('public')
                ->fileAttachmentsDirectory(fn ($get) => 'events/' . ($get('slug') ?? 'default') . '/media-descriptions')
                ->fileAttachmentsVisibility('public'),
                
            Forms\Components\DateTimePicker::make('start_date')
                ->required(),
            Forms\Components\DateTimePicker::make('end_date'),
            Forms\Components\TextInput::make('location')
                ->maxLength(255),
            Forms\Components\TextInput::make('map_url')
                ->label('Google Map Embed URL')
                ->maxLength(1000),
            Forms\Components\TextInput::make('youtube_link')
                ->label('YouTube Live Link (Optional)')
                ->maxLength(255),
            Forms\Components\TextInput::make('meeting_link')
                ->label('Online Meeting Link (Zoom/Meet)')
                ->maxLength(1000)
                ->url(),
            Forms\Components\TextInput::make('price')
                ->label('Price (e.g. Free, $50)')
                ->maxLength(255),
            Forms\Components\TextInput::make('quota')
                ->numeric()
                ->default(0),
            Forms\Components\Select::make('organizer_id')
                ->relationship('organizerInfo', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\FileUpload::make('thumbnail')
                ->image()
                ->disk('public')
                ->visibility('public')
                ->maxSize(2048)
                ->downloadable()
                ->openable()
                ->helperText('Ukuran maks: 2MB.')
                ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, $get): string {
                    $slug = $get('slug') ?: 'event';
                    return \App\Helpers\UploadHelper::getSluggedFilename($file, 'events/' . $slug . '/thumbnail');
                })
                ->preserveFilenames(),
            Forms\Components\Toggle::make('is_active')
                ->required(),
            Forms\Components\Toggle::make('is_certificate_available')
                ->label('Certificate Available')
                ->default(false),
            Forms\Components\Repeater::make('speakers')
                ->schema([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('role')->required(),
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->disk('public')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->downloadable()
                        ->openable()
                        ->helperText('Ukuran maks: 2MB.')
                        ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, $get): string {
                            $slug = $get('../../slug') ?: 'event';
                            return \App\Helpers\UploadHelper::getSluggedFilename($file, 'events/' . $slug . '/speakers');
                        })
                        ->preserveFilenames(),
                    Forms\Components\TextInput::make('linkedin_link')->url()->label('LinkedIn'),
                    Forms\Components\TextInput::make('instagram_link')->url()->label('Instagram'),
                    Forms\Components\TextInput::make('tiktok_link')->url()->label('TikTok'),
                ])
                ->columnSpanFull(),
            Forms\Components\Repeater::make('schedule')
                ->schema([
                    Forms\Components\TextInput::make('time')
                        ->label('Time (e.g. 09:00 AM)')
                        ->required(),
                    Forms\Components\TextInput::make('activity')
                        ->required(),
                ])
                ->columnSpanFull(),
            Forms\Components\Repeater::make('faq')
                ->label('Frequently Asked Questions')
                ->schema([
                    Forms\Components\TextInput::make('question')->required(),
                    Forms\Components\Textarea::make('answer')->required(),
                ])
                ->columnSpanFull(),
        ];
    }
}
