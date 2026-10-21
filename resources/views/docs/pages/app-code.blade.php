@extends('docs::layout')

@section('page', 'Application code')
@section('lead', 'Services, helpers, jobs, config and providers, and the Laravel idioms preferred across them.')

@section('content')
    <x-docs::section id="structure" title="Structure">
        <x-docs::rule title="Services">
            <p>A service with one responsibility takes its inputs in a promoted-property constructor, is created with <code>new</code> at the call site and exposes <code>handle()</code>. A service that offers several independent read or report operations has a static <code>make()</code> factory and descriptively named methods, called as <code>Service::make()-&gt;method()</code>. Services are for read-side aggregation and reports; business logic belongs in a method on the model.</p>
            <x-docs::code lang="php" title="app/Services/Dashboard.php">
                @verbatim
                use App\Services\Dashboard;

                $totals = Dashboard::make()->totals();
                @endverbatim
            </x-docs::code>

        </x-docs::rule>
        <x-docs::rule title="Helpers and services">
            <p>A stateless utility with no dependencies (formatting, casting, paths) is a global function guarded by <code>function_exists()</code>. Anything that needs the container, a view or shared state is a service class, with a facade when a static entry point is needed. Formatting helpers are forgiving: they accept null or bad input and return it unchanged, never throw, and date helpers return a string, not a <code>Carbon</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Jobs end in Job">
            <p>Name every queued job with a <code>Job</code> suffix, such as <code>SendInvoicesJob</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Config: url or uri">
            <p>A config key holding a full address with scheme and host is <code>url</code> (<code>base_url</code>, <code>api_url</code>). A key holding a relative path resolved against a base is <code>uri</code> (<code>api_uri</code>). Never mix them.</p>
        </x-docs::rule>
        <x-docs::rule title="Use the shared traits">
            <p>Take <code>HasUlids</code>, <code>HasUploads</code>, <code>HasProfileImage</code>, <code>HasEnumElements</code>, <code>HasNumberIdentifiers</code> and <code>HasMoney</code> from <code>Nyumbani\Essentials\Traits</code> instead of keeping a copy. Only app-specific traits live in <code>app/Traits</code>. Model mixins are <code>HasX</code> traits using <code>bootHasX()</code> and <code>initializeHasX()</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Events are for messages">
            <p>Dispatch an event only to decouple notification delivery (SMS or email) from the workflow that triggers it. For everything else, call services and models directly.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="idioms" title="Laravel idioms">
        <x-docs::rule title="DB::transaction() closures">
            <p>Put every create, update and delete in a <code>DB::transaction()</code>, together with what is written alongside it, such as the activity log entry. That holds for a single write too, because its log entry is a second write. Never call <code>beginTransaction()</code>, <code>commit()</code> or <code>rollBack()</code> by hand. Keep what cannot be rolled back after the transaction: SMS, email, jobs, events, HTTP calls and the redirect.</p>
            <x-docs::code lang="php" title="app/Http/Controllers/Admins/CustomerController.php">
                @verbatim
                use Illuminate\Support\Facades\DB;

                //  Delete the customer and log it as one unit
                DB::transaction(function () use ($customer) {
                    //  Delete the customer
                    $customer->delete();

                    //  Log the deletion
                    activity()
                        ->performedOn($customer)
                        ->event('customer-deleted')
                        ->log('Deleted customer '.$customer->name);
                });

                //  Tell the user, once the changes are saved
                return redirect()
                    ->route('customers.index')
                    ->alert('success', trans('alerts.customers.deleted', ['name' => $customer->name]));
                @endverbatim
            </x-docs::code>

        </x-docs::rule>
        <x-docs::rule title="Inject, do not locate">
            <p>Use constructor or method injection, an explicit <code>new X(...)</code>, or the class's own <code>make()</code>. Do not use <code>app()</code> or <code>resolve()</code> as a service locator. The one exception is <code>app(Class::class, [...])</code> for a class that needs runtime constructor arguments.</p>
        </x-docs::rule>
        <x-docs::rule title="Helpers over facades">
            <p>Prefer <code>route()</code>, <code>view()</code>, <code>redirect()</code>, <code>config()</code>, <code>auth()</code>, <code>response()</code>, <code>now()</code>, <code>today()</code> and the shared helpers (<code>user()</code>, <code>money()</code>, <code>prettify()</code>). Keep <code>Gate::</code> for authorization and <code>DB::</code> and <code>Storage::</code> for side effects.</p>
        </x-docs::rule>
        <x-docs::rule title="route() for every URL, with named parameters">
            <p>Generate URLs with named routes, never <code>url()</code> or <code>action()</code>. Pass parameters as a keyed array, never a bare model.</p>
            <x-docs::code lang="php" title="app/Http/Controllers/CustomerController.php">
                @verbatim
                route('customers.show', ['customer' => $customer]);   // yes
                route('customers.show', $customer);                   // no
                @endverbatim
            </x-docs::code>

        </x-docs::rule>
        <x-docs::rule title="Collections and fluent strings">
            <p>Use <code>collect()-&gt;map()-&gt;filter()</code> chains instead of <code>array_map</code> and <code>array_filter</code>, and <code>str()</code> instead of <code>Str::</code> or native string functions for producing strings. Use <code>foreach</code> only for side effects.</p>
        </x-docs::rule>
        <x-docs::rule title="Enums over constants and switch">
            <p>Represent a fixed set of options with a string-backed enum and <code>match</code>, never class constants or <code>switch</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Query helpers">
            <p>Scope to a related model with <code>whereBelongsTo($model)</code> instead of <code>where('x_id', $model-&gt;id)</code>. When a record may or may not exist, use <code>updateOrCreate()</code> or <code>firstOrCreate()</code> on a natural unique key, never fetch and then <code>save()</code>. When eager-loading more than one relation with <code>-&gt;load([...])</code>, put each on its own line; <code>-&gt;with([...])</code> stays inline.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="dates-money" title="Dates and money">
        <x-docs::rule title="now() and today()">
            <p>Use <code>now()</code> and <code>today()</code> for the current moment. Use <code>Carbon::parse()</code>, <code>create()</code> or <code>createFromFormat()</code> only for dates that arrive from outside.</p>
        </x-docs::rule>
        <x-docs::rule title="Copy a Carbon date before changing it">
            <p>Carbon dates are mutable, so <code>startOfDay()</code>, <code>addDay()</code> and every other modifier changes the instance. Call <code>-&gt;copy()</code> first whenever the original must survive. Do not switch to <code>CarbonImmutable</code>.</p>
            <x-docs::code lang="php" title="app/Services/Report.php">
                @verbatim
                $end = $from->copy()->endOfMonth();
                @endverbatim
            </x-docs::code>

        </x-docs::rule>
        <x-docs::rule title="Periods use the request macro">
            <p>Parse a report's period (a day, month, year or a <code>from to to</code> range) with <code>request()-&gt;period('month')</code>, which returns the <code>from</code> and <code>to</code> dates. Never parse the input by hand.</p>
        </x-docs::rule>
        <x-docs::rule title="Never hardcode currency">
            <p>Use <code>money()</code> and <code>currency()</code>, or a model's <code>HasMoney</code> methods, so display follows <code>config('essentials.currency')</code>. Never write "KES" or "Ksh" in PHP output, messages or PDFs.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="providers" title="Providers">
        <p>The package applies the app-wide defaults itself, so an app's providers hold only what is its own.</p>
        <x-docs::rule title="What lives where">
            <p><code>AppServiceProvider</code> holds the morph map and app-only macros. <code>FortifyServiceProvider</code> holds the app's own Fortify overrides, such as role-based login and logout responses. <code>ViewServiceProvider</code> holds the app's own view composers and directives. <code>AuthServiceProvider</code> holds only gates that are not model-backed. Do not repeat the package defaults (see App defaults).</p>
        </x-docs::rule>
        <x-docs::rule title="Lazy loading is prevented">
            <p>Outside production an unloaded relation throws <code>LazyLoadingViolationException</code>, locally and in tests. Eager-load whatever you read.</p>
        </x-docs::rule>
        <x-docs::rule title="The morph map covers every model">
            <p>Register each model in <code>Relation::enforceMorphMap()</code> with a kebab-case key.</p>
        </x-docs::rule>
    </x-docs::section>
@endsection
