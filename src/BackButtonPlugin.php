<?php

declare(strict_types=1);

namespace MarcelWeidum\BackButton;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
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
            $this->getPageHeaderRenderHook(),
            function ($scopes): ?View {
                $scopes = collect($scopes);

                $isEditOrView = $scopes->contains(
                    fn (string $scope): bool => $this->isResourceEditOrViewPage($scope)
                );

                if (! $isEditOrView || ! $this->shouldRenderForScopes($scopes)) {
                    return null;
                }

                return view('filament-back-button::back-button');
            }
        );
    }

    private function getPageHeaderRenderHook(): string
    {
        $headingBeforeHook = PanelsRenderHook::class.'::PAGE_HEADER_HEADING_BEFORE';

        if (defined($headingBeforeHook)) {
            $hook = constant($headingBeforeHook);

            if (is_string($hook)) {
                return $hook;
            }
        }

        return PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE;
    }

    private function shouldRenderForScopes(Collection $scopes): bool
    {
        if (config('back-button.all_resources', true)) {
            return true;
        }

        $allowedResources = collect(config('back-button.resources', []))
            ->map(fn (string $resource): string => $this->normalizeResourceClass($resource));

        if ($allowedResources->isEmpty()) {
            return false;
        }

        return $scopes
            ->flatMap(fn (string $scope): array => $this->resolveResourcesFromScope($scope))
            ->filter()
            ->contains(fn (string $resource): bool => $allowedResources->contains(
                $this->normalizeResourceClass($resource),
            ));
    }

    private function isResourceEditOrViewPage(string $scope): bool
    {
        if (is_a($scope, EditRecord::class, true) || is_a($scope, ViewRecord::class, true)) {
            return true;
        }

        return Str::contains($scope, ['\\Pages\\Edit', '\\Pages\\View']);
    }

    /**
     * @return array<string>
     */
    private function resolveResourcesFromScope(string $scope): array
    {
        if (! Str::contains($scope, '\\Resources\\')) {
            return [];
        }

        $resources = [$scope];

        if (Str::contains($scope, '\\Pages\\')) {
            [$resource] = explode('\\Pages\\', $scope);

            $resources[] = $resource;
        }

        return $resources;
    }

    private function normalizeResourceClass(string $resource): string
    {
        return mb_ltrim($resource, '\\');
    }
}
