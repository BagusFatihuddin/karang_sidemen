<?php

namespace App\Filament\Admin\Resources\Visitors\Pages;

use App\Filament\Admin\Resources\Visitors\VisitorResource;
use Filament\Resources\Pages\ListRecords;

class ListVisitors extends ListRecords
{
    protected static string $resource =
        VisitorResource::class;
}