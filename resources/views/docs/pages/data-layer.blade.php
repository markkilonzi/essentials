@extends('docs::layout')

@section('page', 'Models and data')
@section('lead', 'Models, migrations, enums, seeders and data tables.')

@section('content')
    <x-docs::section id="models" title="Models">
        <x-docs::rule title="Fillable allow-lists">
            <p>Declare <code>protected $fillable</code> with the exact assignable columns. Never use <code>$guarded</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Two keys">
            <p>The auto-increment <code>id</code> is the internal key. A unique <code>ulid</code> column is the public route key, through <code>Nyumbani\Essentials\Traits\HasUlids</code> (not Laravel's built-in trait). Every model whose table has a <code>ulid</code> uses it; pivot tables without one are exempt. Never read <code>$model-&gt;ulid</code> in views, controllers or requests; use <code>$model-&gt;getRouteKey()</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Member order">
            <p><code>boot()</code> or <code>booted()</code>, <code>casts()</code> and <code>$fillable</code> first, then the Attribute accessors as one block, then relationships, then other methods. Every declared property carries a one-line doc block.</p>
        </x-docs::rule>
        <x-docs::rule title="Accessors are get-only">
            <p>Define computed values with <code>Attribute::make(get: ...)</code>. Never pass a <code>set:</code> closure; transformation on write belongs in a cast class or a <code>casts()</code> type.</p>
        </x-docs::rule>
        <x-docs::rule title="Scopes use the Scope attribute">
            <p>Define scopes with the <code>#[Scope]</code> attribute on a protected method, never <code>scopeXxx()</code> names.</p>
            <x-docs::code lang="php" title="app/Models/Customer.php">
                @verbatim
                use Illuminate\Database\Eloquent\Attributes\Scope;
                use Illuminate\Database\Eloquent\Builder;

                #[Scope]
                protected function active(Builder $query): void
                {
                    //  Keep the active customers
                    $query->where('is_active', true);
                }
                @endverbatim
            </x-docs::code>

        </x-docs::rule>
        <x-docs::rule title="Lifecycle hooks in booted()">
            <p>Use <code>booted()</code> closures or trait boot methods, not observer classes or <code>#[ObservedBy]</code>. Keep each closure short: put the work in a model method and call it, <code>static::saving(fn (self $model) =&gt; $model-&gt;setNumber())</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Relationships only when needed">
            <p>Add a relationship only when something uses it, and do not define the inverse by default. Relations must match real columns: when a table has no tenant column, reach the tenant with <code>hasManyThrough</code> or <code>hasOneThrough</code>. Pivot relations declare <code>withPivot()</code> for extra columns and <code>withTimestamps()</code> when present.</p>
        </x-docs::rule>
        <x-docs::rule title="Tenant data through the tenant">
            <p>Query tenant-owned data from the tenant's relation, <code>$tenant-&gt;services()-&gt;latest('id')-&gt;get()</code>, not <code>Service::whereBelongsTo($tenant)</code>. Never list another tenant's rows.</p>
        </x-docs::rule>
        <x-docs::rule title="The User model knows its home">
            <p>The <code>User</code> model implements <code>Nyumbani\Essentials\Contracts\HasHomeRoute</code>. <code>homeRoute()</code> always returns a URL, chosen by role or permission. A user with no access gets the unauthorized page or a 403, never <code>null</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="No $with">
            <p>Never set <code>$with</code>. Eager-load explicitly per query.</p>
        </x-docs::rule>
        <x-docs::rule title="Casts for normalised values">
            <p>Put value normalisation, such as phone numbers or names, in a cast class under <code>app/Casts</code> referenced from <code>casts()</code>, not in an inline <code>Attribute::make()</code>.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="migrations" title="Migrations">
        <x-docs::rule title="ULID right after id">
            <p>New tables use <code>$table-&gt;id()</code> immediately followed by <code>$table-&gt;ulid()-&gt;unique()</code>, paired with the model's <code>HasUlids</code> trait.</p>
            <x-docs::code lang="php" title="database/migrations/create_customers_table.php">
                @verbatim
                use App\Models\Organization;
                use Illuminate\Database\Schema\Blueprint;
                use Illuminate\Support\Facades\Schema;

                Schema::create('customers', function (Blueprint $table) {
                    $table->id();
                    $table->ulid()->unique();
                    $table->foreignIdFor(Organization::class)->constrained();
                    $table->string('status');
                    $table->timestamps();
                });
                @endverbatim
            </x-docs::code>

        </x-docs::rule>
        <x-docs::rule title="foreignIdFor()">
            <p>Use <code>$table-&gt;foreignIdFor(Model::class)-&gt;constrained()</code>, adding <code>-&gt;nullable()</code> when optional, for pivot and child tables too. Never <code>foreignId('x_id')</code> or a manual <code>foreign()-&gt;references()</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Enum columns are strings">
            <p>Store enum-backed values with <code>$table-&gt;string()</code> and cast them to the enum in the model. Never <code>-&gt;enum()</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="A real down()">
            <p>Every migration implements <code>down()</code>, typically <code>Schema::dropIfExists</code>.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="enums" title="Enums">
        <x-docs::rule title="String-backed, UPPER_SNAKE_CASE">
            <p>Declare <code>enum X: string</code> with UPPER_SNAKE_CASE cases, kebab-case values and the <code>Nyumbani\Essentials\Traits\HasEnumElements</code> trait, which gives <code>label()</code>, <code>lowerLabel()</code> and <code>random()</code>. Add domain methods, such as <code>badgeClasses()</code>, with <code>match ($this)</code>.</p>
            <x-docs::code lang="php" title="app/Enums/CustomerStatus.php">
                @verbatim
                use Nyumbani\Essentials\Traits\HasEnumElements;

                enum CustomerStatus: string
                {
                    use HasEnumElements;

                    case ACTIVE = 'active';
                    case ON_HOLD = 'on-hold';

                    /**
                     * Get the badge classes
                     *
                     * @author Jane Doe <jane@example.com>
                     */
                    public function badgeClasses(): string
                    {
                        return match ($this) {
                            self::ACTIVE => 'bg-green-100',
                            self::ON_HOLD => 'bg-gray-100',
                        };
                    }
                }
                @endverbatim
            </x-docs::code>

        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="seeders" title="Seeders and permissions">
        <x-docs::rule title="Seed data comes from JSON">
            <p>Seed data lives in <code>database/seeders/data/*.json</code>: <code>permissions.json</code> (role, then group, then permission names), <code>system-admins.json</code> and <code>organizations.json</code>. Passwords in these files are bcrypt hashes. The package's <code>PermissionSeeder</code> syncs roles and permissions from <code>permissions.json</code>; run it with <code>php artisan nyumbani:permissions</code>.</p>
        </x-docs::rule>
        <x-docs::rule title="Permission names">
            <p>Permissions are kebab-case verb-noun: plural for a list (<code>view-users</code>), singular for one record (<code>create-user</code>).</p>
        </x-docs::rule>
        <x-docs::rule title="Defaults through the user">
            <p>Give a user their role and its permissions with <code>$user-&gt;assignDefaultPermissions($role)</code>. Never call <code>syncPermissions()</code> or <code>givePermissionTo()</code> directly to set defaults.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="data-tables" title="Data tables">
        <x-docs::rule title="Inline unless told otherwise">
            <p>Build every table as an inline table: a class extending <code>BaseTable</code>, created with <code>php artisan make:data-table Name</code> and rendered with <code>&lt;x-data-table :payload="..."&gt;</code>. The browser searches and sorts the rows. Use a server-side table (<code>--server-side</code>) or an API table (<code>--api</code>) only when the request asks for it. If an inline table might hold thousands of rows, say so and still build it inline.</p>
            <x-docs::code lang="bash" title="Terminal">
                php artisan make:data-table Customers
                php artisan make:data-table Customers --server-side
                php artisan make:data-table Customers --api
            </x-docs::code>
        </x-docs::rule>
        <x-docs::rule title="Newest first, never paginated">
            <p>Order <code>query()</code> with <code>-&gt;latest('id')</code>, qualifying the column (<code>users.id</code>) when the query joins. Return the unpaginated query: never call <code>paginate()</code>, <code>simplePaginate()</code> or <code>cursorPaginate()</code>, because <code>amprest/dt-tables</code> handles paging.</p>
        </x-docs::rule>
        <x-docs::rule title="Scripts do not run inside rows">
            <p>Rows and row actions are inserted with <code>innerHTML</code>, so a <code>&lt;script&gt;</code> in them never runs and a <code>@@push('js')</code> made while rendering a row never reaches the layout. For a modal or form rendered per row, use an inline <code>x-data="{ ... }"</code> object and <code>x-on:</code> handlers, or register the component in a file in <code>resources/js</code> that the app imports once.</p>
        </x-docs::rule>
    </x-docs::section>
@endsection
