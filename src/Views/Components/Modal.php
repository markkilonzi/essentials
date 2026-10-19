<?php

namespace Nyumbani\Essentials\Views\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * The panel widths by size
     *
     * @var array<string, string>
     */
    protected const WIDTHS = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-3xl',
        'xl' => 'max-w-5xl',
        'full' => 'max-w-none',
    ];

    /**
     * Whether the panel slides in from the right
     */
    public bool $isSlideOver;

    /**
     * Whether the modal reopens because its named error bag has errors
     */
    public bool $reopen;

    /**
     * Create the modal
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __construct(
        public bool $closeButton = true,
        public bool $showTitle = true,
        public string $size = 'md',
        public string $position = 'center',
        public ?string $name = null,
        public ?string $errorBag = null,
        public bool $dismissable = true,
        public ?string $src = null,
    ) {
        //  Check whether the panel slides in from the right
        $this->isSlideOver = $position === 'right';

        //  Reopen when the named error bag has errors after a failed save
        $this->reopen = $errorBag !== null && (bool) session('errors')?->hasBag($errorBag) && session('errors')->getBag($errorBag)->any();
    }

    /**
     * Get the width class of the panel
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function width(): string
    {
        //  Fill a slide-over with its own default width
        if ($this->isSlideOver && $this->size === 'md') {
            return 'max-w-md';
        }

        return self::WIDTHS[$this->size] ?? $this->size;
    }

    /**
     * Get the classes of the panel
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function panelClasses(): array
    {
        return [
            'relative flex w-full flex-col overflow-hidden bg-white shadow-lg',
            $this->width(),
            'h-dvh rounded-none' => $this->isSlideOver,
            'max-h-[calc(100dvh-1rem)] rounded-t-xl lg:max-h-[calc(100dvh-3rem)] lg:rounded' => ! $this->isSlideOver,
        ];
    }

    /**
     * Get the transition of the panel: a slide from the right, or a bottom sheet on small screens and a fade on large ones
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function transition(): array
    {
        //  Slide in from the right
        if ($this->isSlideOver) {
            return [
                'x-transition:enter' => 'transition duration-300 ease-out',
                'x-transition:enter-start' => 'translate-x-full',
                'x-transition:enter-end' => 'translate-x-0',
                'x-transition:leave' => 'transition duration-200 ease-in',
                'x-transition:leave-start' => 'translate-x-0',
                'x-transition:leave-end' => 'translate-x-full',
            ];
        }

        return [
            'x-transition:enter' => 'transition duration-300 ease-out',
            'x-transition:enter-start' => 'translate-y-full opacity-0 lg:translate-y-0 lg:scale-95',
            'x-transition:enter-end' => 'translate-y-0 opacity-100 lg:scale-100',
            'x-transition:leave' => 'transition duration-200 ease-in',
            'x-transition:leave-start' => 'translate-y-0 opacity-100 lg:scale-100',
            'x-transition:leave-end' => 'translate-y-full opacity-0 lg:translate-y-0 lg:scale-95',
        ];
    }

    /**
     * Get the view
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function render(): View
    {
        return view('essentials::components.modal');
    }
}
