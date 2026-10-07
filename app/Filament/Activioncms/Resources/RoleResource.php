<?php

namespace App\Filament\Activioncms\Resources;

use App\Filament\Activioncms\Resources\RoleResource\Pages;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';
    protected static string|\UnitEnum|null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Roles & Permissions';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $pluralModelLabel = 'Roles & Permissions';

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('administrator') ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->hasRole('administrator') ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->hasRole('administrator') && $record->name !== 'administrator';
    }

    public static function canDelete($record): bool
    {
        $protected = ['administrator', 'co-admin', 'editor', 'viewer'];
        return Auth::user()?->hasRole('administrator') && !in_array($record->name, $protected);
    }

    public static function getPermissionModules(): array
    {
        return [
            'news' => [
                'label' => 'News Management',
                'icon'  => 'heroicon-o-newspaper',
                'permissions' => [
                    'view_any_news'          => 'View Any News',
                    'view_news'              => 'View Details',
                    'create_news'            => 'Create News',
                    'update_news'            => 'Update News',
                    'delete_news'            => 'Delete News',
                    'delete_any_news'        => 'Delete Any News',
                    'view_any_news_category' => 'View Categories',
                    'view_news_category'     => 'Category Details',
                    'create_news_category'   => 'Create Category',
                    'update_news_category'   => 'Update Category',
                    'delete_news_category'   => 'Delete Category',
                    'view_any_news_tag'      => 'View Tags',
                    'view_news_tag'          => 'Tag Details',
                    'create_news_tag'        => 'Create Tag',
                    'update_news_tag'        => 'Update Tag',
                    'delete_news_tag'        => 'Delete Tag',
                ],
            ],
            'projects' => [
                'label' => 'Projects Management',
                'icon'  => 'heroicon-o-folder',
                'permissions' => [
                    'view_any_project'   => 'View Any Projects',
                    'view_project'       => 'View Details',
                    'create_project'     => 'Create Project',
                    'update_project'     => 'Update Project',
                    'delete_project'     => 'Delete Project',
                    'delete_any_project' => 'Delete Any Project',
                ],
            ],
            'cms' => [
                'label' => 'CMS / Pages Management',
                'icon'  => 'heroicon-o-document-text',
                'permissions' => [
                    'view_any_page'   => 'View Any Pages',
                    'view_page'       => 'View Details',
                    'create_page'     => 'Create Page',
                    'update_page'     => 'Update Page',
                    'delete_page'     => 'Delete Page',
                    'delete_any_page' => 'Delete Any Page',
                ],
            ],
            'services' => [
                'label' => 'Services Management',
                'icon'  => 'heroicon-o-wrench-screwdriver',
                'permissions' => [
                    'view_any_service'   => 'View Any Services',
                    'view_service'       => 'View Details',
                    'create_service'     => 'Create Service',
                    'update_service'     => 'Update Service',
                    'delete_service'     => 'Delete Service',
                    'delete_any_service' => 'Delete Any Service',
                ],
            ],
            'products' => [
                'label' => 'Product Catalog',
                'icon'  => 'heroicon-o-shopping-bag',
                'permissions' => [
                    'view_any_product'   => 'View Any Products',
                    'view_product'       => 'View Details',
                    'create_product'     => 'Create Product',
                    'update_product'     => 'Update Product',
                    'delete_product'     => 'Delete Product',
                    'delete_any_product' => 'Delete Any Product',
                    'view_any_brand'     => 'View Brands',
                    'view_brand'         => 'Brand Details',
                    'create_brand'       => 'Create Brand',
                    'update_brand'       => 'Update Brand',
                    'delete_brand'       => 'Delete Brand',
                ],
            ],
            'clients' => [
                'label' => 'Clients Management',
                'icon'  => 'heroicon-o-user-group',
                'permissions' => [
                    'view_any_client'   => 'View Any Clients',
                    'view_client'       => 'View Details',
                    'create_client'     => 'Create Client',
                    'update_client'     => 'Update Client',
                    'delete_client'     => 'Delete Client',
                    'delete_any_client' => 'Delete Any Client',
                ],
            ],
            'events' => [
                'label' => 'Events Management',
                'icon'  => 'heroicon-o-calendar',
                'permissions' => [
                    'view_any_event'   => 'View Any Events',
                    'view_event'       => 'View Details',
                    'create_event'     => 'Create Event',
                    'update_event'     => 'Update Event',
                    'delete_event'     => 'Delete Event',
                    'delete_any_event' => 'Delete Any Event',
                ],
            ],
            'campaign' => [
                'label' => 'Campaign Management',
                'icon'  => 'heroicon-o-megaphone',
                'permissions' => [
                    'view_any_campaign' => 'View Any Campaigns',
                    'view_campaign'     => 'View Details',
                    'create_campaign'   => 'Create Campaign',
                    'update_campaign'   => 'Update Campaign',
                    'delete_campaign'   => 'Delete Campaign',
                ],
            ],
            'analytics_seo' => [
                'label' => 'Analytics & SEO',
                'icon'  => 'heroicon-o-chart-bar',
                'permissions' => [
                    'view_analytics'  => 'View Analytics',
                    'view_seo'        => 'View SEO',
                    'update_seo'      => 'Update SEO',
                    'view_search'     => 'View Search',
                    'update_search'   => 'Update Search',
                    'view_whatsapp'   => 'View WhatsApp',
                    'update_whatsapp' => 'Update WhatsApp',
                ],
            ],
            'settings_site' => [
                'label' => 'Settings & Forms & Menu',
                'icon'  => 'heroicon-o-cog-6-tooth',
                'permissions' => [
                    'view_settings'    => 'View Settings',
                    'update_settings'  => 'Update Settings',
                    'view_any_menu'    => 'View Any Menus',
                    'view_menu'        => 'View Menu Details',
                    'create_menu'      => 'Create Menu',
                    'update_menu'      => 'Update Menu',
                    'delete_menu'      => 'Delete Menu',
                    'view_any_form'    => 'View Any Forms',
                    'view_form'        => 'View Form Details',
                    'create_form'      => 'Create Form',
                    'update_form'      => 'Update Form',
                    'delete_form'      => 'Delete Form',
                ],
            ],
            'users' => [
                'label' => 'User & Role Management',
                'icon'  => 'heroicon-o-shield-check',
                'permissions' => [
                    'view_any_user'   => 'View Any Users',
                    'view_user'       => 'View Details',
                    'create_user'     => 'Create User',
                    'update_user'     => 'Update User',
                    'delete_user'     => 'Delete User',
                    'delete_any_user' => 'Delete Any User',
                    'view_any_role'   => 'View Any Roles',
                    'view_role'       => 'View Role Details',
                    'create_role'     => 'Create Role',
                    'update_role'     => 'Update Role',
                    'delete_role'     => 'Delete Role',
                ],
            ],
        ];
    }

    public static function form(Schema $schema): Schema
    {
        $modules = static::getPermissionModules();
        $moduleSections = [];

        foreach ($modules as $key => $module) {
            $permKeys = array_keys($module['permissions']);

            $moduleSections[] = Section::make($module['label'])
                ->icon($module['icon'])
                ->collapsible()
                ->compact()
                ->headerActions([
                    Action::make("select_all_{$key}")
                        ->label('Select All')
                        ->icon('heroicon-m-check')
                        ->size('xs')
                        ->color('success')
                        ->action(function ($set) use ($key, $permKeys) {
                            $set("permissions_{$key}", $permKeys);
                        }),
                    Action::make("deselect_all_{$key}")
                        ->label('Clear')
                        ->icon('heroicon-m-x-mark')
                        ->size('xs')
                        ->color('gray')
                        ->action(function ($set) use ($key) {
                            $set("permissions_{$key}", []);
                        }),
                ])
                ->schema([
                    CheckboxList::make("permissions_{$key}")
                        ->hiddenLabel()
                        ->options($module['permissions'])
                        ->columns([
                            'default' => 1,
                            'sm'      => 2,
                        ])
                        ->dehydrated(false),
                ]);
        }

        return $schema
            ->columns(1)
            ->components([
                Section::make('Role Details')
                    ->description('Nama dan identitas role sistem.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Role Name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->helperText('Gunakan huruf kecil dengan tanda minus: contoh content-writer')
                            ->disabled(fn ($record) => $record && in_array($record->name, ['administrator', 'co-admin', 'editor', 'viewer'])),
                    ])
                    ->columnSpanFull(),

                Section::make('Permissions Matrix')
                    ->description('Kelola hak akses untuk setiap modul. Anda dapat memilih seluruh modul sekaligus atau menggunakan tombol pada masing-masing kartu.')
                    ->headerActions([
                        Action::make('checkAllGlobal')
                            ->label('Select All Modules')
                            ->icon('heroicon-m-check-badge')
                            ->color('success')
                            ->size('sm')
                            ->button()
                            ->action(function ($set) use ($modules) {
                                foreach ($modules as $key => $module) {
                                    $set("permissions_{$key}", array_keys($module['permissions']));
                                }
                            }),
                        Action::make('uncheckAllGlobal')
                            ->label('Clear All Modules')
                            ->icon('heroicon-m-x-circle')
                            ->color('gray')
                            ->size('sm')
                            ->button()
                            ->action(function ($set) use ($modules) {
                                foreach ($modules as $key => $module) {
                                    $set("permissions_{$key}", []);
                                }
                            }),
                    ])
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md'      => 2,
                            '2xl'     => 3,
                        ])->schema($moduleSections),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role Name')
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('-', ' ', $state)))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'administrator' => 'danger',
                        'co-admin'      => 'warning',
                        'editor'        => 'success',
                        'viewer'        => 'gray',
                        default         => 'info',
                    }),
                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->badge()
                    ->color('gray')
                    ->suffix(' permissions'),
                TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->badge()
                    ->color('primary')
                    ->suffix(' users'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->actions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->name === 'administrator'),
                DeleteAction::make()
                    ->hidden(fn ($record) => in_array($record->name, ['administrator', 'co-admin', 'editor', 'viewer'])),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
