<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ChatPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $title = 'Chat Interno';

    protected static ?string $navigationLabel = 'Chat';

    protected static string $view = 'filament.pages.chat-page';
}
