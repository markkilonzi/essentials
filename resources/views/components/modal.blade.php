@use('Illuminate\View\ComponentAttributeBag')

<div x-data="essentialsModal({ name: @js($name), src: @js($src), reopen: @js($reopen) })" x-modelable="open" x-on:open-modal.window="show($event.detail)" x-on:close-modal.window="hide($event.detail)" {{ $attributes->only(['wire:model', 'x-model']) }}>
    {{--  Trigger  --}}
    @isset($trigger)
        <div x-on:click="open = true" class="cursor-pointer">
            {{ $trigger }}
        </div>
    @endisset

    {{--  Teleported to the body so the modal always stacks above the rest of the page  --}}
    <template x-teleport="body">
        {{--  A modal that is not dismissable is driven by :open, so Escape and outside clicks only raise a close event nobody listens to  --}}
        <div x-dialog @if ($dismissable) x-model="open" @else :open="open" @endif style="display: none" class="fixed inset-0 z-50 overflow-y-auto overflow-x-hidden text-left">
            {{--  Overlay  --}}
            <div x-dialog:overlay x-transition:enter.opacity class="fixed inset-0 bg-black/70"></div>

            {{--  Panel: a bottom sheet on small screens, centred on large ones, or a slide-over  --}}
            <div @class(['relative flex min-h-full overflow-clip text-gray-600', 'items-stretch justify-end' => $isSlideOver, 'items-end justify-center p-0 lg:items-start lg:p-6' => ! $isSlideOver])>
                <div x-dialog:panel {{ new ComponentAttributeBag($transition()) }} {{ $attributes->only('class')->class($panelClasses()) }}>
                    @if ($closeButton)
                        <div class="absolute right-0 top-0 z-10 mr-1 mt-1 flex cursor-pointer items-center justify-center p-4" @if ($dismissable) x-on:click="$dialog.close(); $dispatch('modal-closed')" @else x-on:click="open = false; $dispatch('modal-closed')" @endif>
                            <button type="button" class="rounded-md bg-gray-50 focus:outline-hidden focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2" aria-label="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M6 14L14 6" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    {{--  Sheet handle, small screens only  --}}
                    @unless ($isSlideOver)
                        <div class="flex shrink-0 justify-center bg-secondary pt-2 lg:hidden" aria-hidden="true"><span class="h-1 w-10 rounded-full bg-white/30"></span></div>
                    @endunless

                    @if ($showTitle)
                        <div class="flex h-14 shrink-0 items-center bg-secondary px-4 pr-12 text-white">
                            <h1 class="text-lg font-light">{{ $attributes->get('title') }}</h1>
                        </div>
                    @endif

                    {{--  Body: scrolls on its own so the title and the footer stay in view  --}}
                    <div class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden wrap-break-word text-base font-light">
                        @if ($src)
                            <div x-show="loading" class="p-6">
                                <x-essentials::loader size="md" message="Nyumbani is loading this for you..." />
                            </div>
                            <div x-show="failed" x-cloak class="p-6 text-center text-red-600">
                                We could not load this. <button type="button" class="underline" x-on:click="load()">Try again</button>
                            </div>
                            <div x-ref="remote"></div>
                        @endif

                        {{ $slot }}
                    </div>

                    {{--  Footer: pinned below the scrolling body  --}}
                    @isset($action)
                        <div class="shrink-0 border-t border-gray-100 bg-gray-50 shadow-[0_-4px_6px_-4px_rgb(0_0_0/0.08)] lg:p-3">
                            {{ $action }}
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </template>
</div>
