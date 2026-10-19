<?php

namespace Nyumbani\Essentials\Views\Components\Docs;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Code extends Component
{
    /**
     * Create the code block
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __construct(public string $lang = 'php', public ?string $title = null) {}

    /**
     * Get the language the highlighter should use, which reads Blade as html
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function language(): string
    {
        return $this->lang === 'blade' ? 'xml' : $this->lang;
    }

    /**
     * Remove the indentation the page gave the code, and the blank lines around it
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function dedent(mixed $code): string
    {
        //  Split the code into lines
        $lines = explode("\n", trim((string) $code, "\n\r"));

        //  Find the smallest indentation after the first line, which the slot has already trimmed
        $indent = collect(array_slice($lines, 1))
            ->filter(fn (string $line) => trim($line) !== '')
            ->map(fn (string $line) => strlen($line) - strlen(ltrim($line)))
            ->min() ?? 0;

        //  Remove it from every line after the first
        return collect($lines)
            ->map(fn (string $line, int $index) => $index === 0 ? $line : substr($line, min($indent, strlen($line) - strlen(ltrim($line)))))
            ->implode("\n");
    }

    /**
     * Render the code block
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function render(): View
    {
        return view('essentials::components.docs.code');
    }
}
