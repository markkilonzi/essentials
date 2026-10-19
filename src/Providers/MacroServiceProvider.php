<?php

namespace Nyumbani\Essentials\Providers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Fluent;
use Illuminate\Support\ServiceProvider;
use Nyumbani\Essentials\Services\DateFilterService;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register the alert macro on redirects and the period macro on requests
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

        //  Macro for Request, which parses the period input into its dates
        Request::macro('period', function (string $default = 'month'): Fluent {
            /** @var Request $this */
            return app(DateFilterService::class)->parse($this->input('period'), $default);
        });
    }
}
