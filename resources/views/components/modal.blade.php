@use('Illuminate\View\ComponentAttributeBag')

<div x-data="essentialsModal({ name: @js($name), src: @js($src), reopen: @js($reopen), confirm: @js($confirm) })" x-modelable="modal.open" x-on:open-modal.window="modal.show($event.detail)" x-on:close-modal.window="modal.hide($event.detail)" {{ $attributes->only(['wire:model', 'x-model']) }}>
    {{--  Trigger  --}}
    @isset($trigger)
        <div x-on:click="modal.open = true" class="cursor-pointer">
            {{ $trigger }}
        </div>
    @endisset

    {{--  Teleported to the body so the modal always stacks above the rest of the page  --}}
    <template x-teleport="body">
        {{--  A modal that is not dismissable is driven by :open, so Escape and outside clicks only raise a close event nobody listens to  --}}
        <div x-dialog @if ($dismissable) x-model="modal.open" @else :open="modal.open" @endif style="display: none" class="fixed inset-0 z-50 overflow-y-auto overflow-x-hidden text-left">
            {{--  Overlay  --}}
            <div x-dialog:overlay x-transition:enter.opacity class="fixed inset-0 bg-black/70"></div>

            {{--  Panel: a bottom sheet on small screens, centred on large ones, or a slide-over  --}}
            <div @class(['relative flex min-h-full overflow-clip text-gray-600', 'items-stretch justify-end' => $isSlideOver, 'items-end justify-center p-0 lg:items-start lg:p-6' => ! $isSlideOver])>
                <div x-dialog:panel @if ($confirm) x-on:submit="modal.submitted($event)" @endif {{ new ComponentAttributeBag($transition()) }} {{ $attributes->only('class')->class($panelClasses()) }}>
                    @if ($closeButton)
                        <div class="absolute right-0 top-0 z-10 mr-1 mt-1 flex cursor-pointer items-center justify-center p-4" @if ($dismissable) x-on:click="$dialog.close(); $dispatch('modal-closed')" @else x-on:click="modal.open = false; $dispatch('modal-closed')" @endif>
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
                    <div x-ref="body" class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden wrap-break-word text-base font-light">
                        @if ($src)
                            <div x-show="modal.loading" class="p-6">
                                <x-essentials::loader size="md" message="Nyumbani is loading this for you..." />
                            </div>
                            <div x-show="modal.failed" x-cloak class="p-6 text-center text-red-600">
                                We could not load this. <button type="button" class="underline" x-on:click="modal.load()">Try again</button>
                            </div>
                            <div x-ref="remote" @if ($confirm) x-show="! modal.confirming" @endif></div>
                        @endif

                        @if ($confirm)
                            {{--  The steps share one grid cell, so one fades out where the other fades in and the height does not jump  --}}
                            <div class="grid">
                                {{--  Step one: the form, hidden but kept in the page during the review so it still posts  --}}
                                <div x-show="! modal.confirming" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-x-4 opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="-translate-x-4 opacity-0" class="col-start-1 row-start-1">{{ $slot }}</div>

                                {{--  Step two: the review  --}}
                                <div x-show="modal.confirming" x-cloak x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="translate-x-4 opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-4 opacity-0" class="col-start-1 row-start-1" x-ref="review" data-slotted="{{ isset($review) && $review->hasActualContent() ? 'true' : 'false' }}">{{ $review ?? '' }}</div>
                            </div>
                        @else
                            {{ $slot }}
                        @endif
                    </div>

                    {{--  Footer: pinned below the scrolling body  --}}
                    @if ($confirm)
                        <div class="shrink-0 border-t border-gray-100 bg-gray-50 shadow-[0_-4px_6px_-4px_rgb(0_0_0/0.08)] p-3">
                            {{--  Step one: Continue checks the form with the browser's own validation  --}}
                            <div x-show="! modal.confirming" class="flex justify-end">
                                <button type="button" class="btn btn-primary" x-on:click="modal.advance()">{{ $continueLabel }}</button>
                            </div>

                            {{--  Step two: go back, or send the form with the action  --}}
                            <template x-if="modal.confirming">
                                <div class="flex items-center justify-between gap-3">
                                    <button type="button" class="btn btn-primary" x-on:click="modal.back()">{{ $backLabel }}</button>
                                    <div>{{ $action ?? '' }}</div>
                                </div>
                            </template>
                        </div>
                    @elseif (isset($action))
                        <div class="shrink-0 border-t border-gray-100 bg-gray-50 shadow-[0_-4px_6px_-4px_rgb(0_0_0/0.08)] lg:p-3">
                            {{ $action }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </template>
</div>
