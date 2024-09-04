<x-filament-widgets::widget class="fi-account-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-3">

            <x-filament::button
                    color="primary"
                    icon="heroicon-m-arrow-path"
                    labeled-from="sm"
                    tag="button"
                    wire:click="update()"
            >
                {{ __('Update') }}
            </x-filament::button>

            <div class="flex-1">
                <h2 class="grid flex-1 text-base font-semibold leading-6 text-gray-950 dark:text-white" dir="ltr">
                    {{ $reset }}
                    <br>
                    {{ $pull }}
                </h2>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
