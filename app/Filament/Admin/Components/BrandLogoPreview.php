<?php

namespace App\Filament\Admin\Components;

use Filament\Schemas\Components\Component;

class BrandLogoPreview extends Component
{
    protected string $view = 'filament.admin.components.brand-logo-preview';

    public static function make(): static
    {
        return app(static::class);
    }
}
