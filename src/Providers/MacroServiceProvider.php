<?php

namespace Nyumbani\Essentials\Providers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register the alert macro on redirects
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function boot(): void
    {
        //  Macro for Redirector (redirect()), which goes back
        Redirector::macro('alert', function (string $type, string $message): RedirectResponse {
            /** @var Redirector $this */
            return $this->back()->with([
                'alert' => [
                    'type' => $type,
                    'message' => $message,
                ],
            ]);
        });

        //  Macro for RedirectResponse (redirect()->back())
        RedirectResponse::macro('alert', function (string $type, string $message): RedirectResponse {
            /** @var RedirectResponse $this */
            return $this->with([
                'alert' => [
                    'type' => $type,
                    'message' => $message,
                ],
            ]);
        });
    }
}
