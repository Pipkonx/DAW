<?php

namespace App\Filament\Widgets;

use App\Models\Practice;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Database\Eloquent\Model;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Practice::class;

    public function fetchEvents(array $fetchInfo): array
    {
        return Practice::query()
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Practice $practice) => [
                    'id' => $practice->id,
                    'title' => $practice->title,
                    'start' => $practice->starts_at,
                    'end' => $practice->ends_at,
                ]
            )
            ->all();
    }

    public static function canView(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
