<?php

declare(strict_types=1);

namespace MarcelWeidum\BackButton;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Collection;
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
            function ($scopes): ?View {
                $scopes = collect($scopes);

                $isEditOrView = $scopes->contains(
                    fn (string $scope) => Str::contains($scope, ['\\Pages\\Edit', '\\Pages\\View'])
                );

                if (! $isEditOrView || ! $this->shouldRenderForScopes($scopes)) {
                    return null;
                }

                return view('filament-back-button::back-button');
            }
        );
    }

    private function shouldRenderForScopes(Collection $scopes): bool
    {
        if (config('back-button.all_resources', true)) {
            return true;
        }

        $allowedResources = collect(config('back-button.resources', []));

        if ($allowedResources->isEmpty()) {
            return false;
        }

        return $scopes
            ->map(fn (string $scope): ?string => $this->resolveResourceFromScope($scope))
            ->filter()
            ->contains(fn (string $resource): bool => $allowedResources->contains($resource));
    }

    private function resolveResourceFromScope(string $scope): ?string
    {
        if (! Str::contains($scope, '\\Resources\\') || ! Str::contains($scope, '\\Pages\\')) {
            return null;
        }

        [$resource] = explode('\\Pages\\', $scope);

        return $resource;
    }
}
