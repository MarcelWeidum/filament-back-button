<?php

declare(strict_types=1);

namespace MarcelWeidum\BackButton;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class BackButtonPlugin implements Plugin
{
    public static function make(): static
    {
        return app(self::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(self::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'filament-back-button';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_HEADER_HEADING_BEFORE,
            function ($scopes): View|string {
                $isEditOrView = collect($scopes)->contains(
                    fn (string $scope) => Str::contains($scope, ['\\Pages\\Edit', '\\Pages\\View'])
                );

                return $isEditOrView
                    ? view('filament-back-button::back-button') : '';
            }
        );
    }
}
