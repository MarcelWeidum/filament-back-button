<style>
    .fi-back-button {
        display: inline-flex;
        margin-inline-end: 0.75rem;
        vertical-align: middle;
    }

    .fi-back-button + .fi-header-heading {
        display: inline-block;
        vertical-align: middle;
    }
</style>

<x-filament::button
    color="gray"
    onclick="window.history.back()"
    class="fi-back-button items-center justify-center text-sm font-medium text-gray-500 transition hover:text-gray-700 focus:outline-none dark:text-gray-400 dark:hover:text-gray-200"
>
    <span aria-hidden="true">&larr;</span>
    <span class="sr-only">Back</span>
</x-filament::button>
