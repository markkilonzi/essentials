<?php

namespace Nyumbani\Essentials\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Sleep;

class DocsSamplesController
{
    /**
     * Save the sample form of the docs, which validates into the error bag the form posts and only raises a toast
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function form(Request $request): RedirectResponse
    {
        //  Validate, which puts the errors in the posted _error_bag
        $validated = $request->validate(
            ['name' => ['required', 'string', 'min:3', 'max:50']],
            [],
            ['name' => 'customer name'],
        );

        //  Tell the reader it worked
        return back()->alert('success', "The customer {$validated['name']} was saved. Nothing is stored: this is a sample.");
    }

    /**
     * Return the body that the sample remote modal loads the first time it opens
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function modalContent(): View
    {
        //  Take a moment, so the loader can be seen
        Sleep::for(1)->second();

        return view('essentials::docs.fragments.modal-content');
    }

    /**
     * Show an error page with its real status code, for the docs to preview
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function error(string $package, string $code): Response
    {
        return response()->view("essentials::errors.{$code}", [], (int) $code);
    }
}
