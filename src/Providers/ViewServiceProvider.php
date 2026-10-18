<?php

namespace Nyumbani\Essentials\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register the package components and the Blade directives.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function boot(): void
    {
        //  Register the package components as <x-essentials::name>
        Blade::anonymousComponentPath(essentials_path('resources/views/components'), 'essentials');

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
