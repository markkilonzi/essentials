<?php

namespace Nyumbani\Essentials\Providers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register the view composers, the package components and the Blade directives.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function boot(): void
    {
        //  Register the view composers
        $this->handleComposers();

        //  Register the package components
        $this->handleAnonymousComponents();

        //  Register the Blade directives
        $this->handleDirectives();
    }

    /**
     * Send guests to login before the portal layout renders, since it needs the signed-in user.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function handleComposers(): void
    {
        View::composer('essentials::layouts.portal', function () {
            //  Let signed-in users through
            if (auth()->check()) {
                return;
            }

            //  Answer requests that expect json with a 401
            abort_if(request()->expectsJson(), 401);

            //  Redirect to login, remembering the page to come back to
            throw new HttpResponseException(redirect()->guest(route('login')));
        });
    }

    /**
     * Register the package components as <x-essentials::name>.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function handleAnonymousComponents(): void
    {
        Blade::anonymousComponentPath(essentials_path('resources/views/components'), 'essentials');
    }

    /**
     * Register the Blade directives that wrap the formatting helpers.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function handleDirectives(): void
    {
        //  Format strings with underscores into perfect strings
        Blade::directive('prettify', fn ($expression) => "<?php echo prettify($expression); ?>");

        //  Format numbers
        Blade::directive('numeric', fn ($expression) => "<?php echo numeric($expression); ?>");

        //  Format amounts to money
        Blade::directive('money', fn ($expression) => "<?php echo money($expression); ?>");

        //  Print the currency code
        Blade::directive('currency', fn () => '<?php echo currency(); ?>');

        //  Format percentage
        Blade::directive('percentage', function ($expression) {
            //  Split the expression into the value and the total
            [$value, $total] = explode(',', preg_replace("/[\(\)\\\"\']/", '', $expression));

            //  Print the percentage
            return "<?php echo percentage(trim($value), trim($total)); ?>";
        });

        //  Format date
        Blade::directive('date', fn ($expression) => "<?php echo format_date($expression); ?>");

        //  Format time
        Blade::directive('time', fn ($expression) => "<?php echo format_time($expression); ?>");

        //  Format date time
        Blade::directive('datetime', fn ($expression) => "<?php echo format_date_time($expression); ?>");

        //  Format date time for humans
        Blade::directive('dateforhumans', fn ($expression) => "<?php echo format_humans($expression); ?>");

        //  Print the error bag input
        Blade::directive('bag', fn ($expression) => "<?php echo bag($expression); ?>");
    }
}
