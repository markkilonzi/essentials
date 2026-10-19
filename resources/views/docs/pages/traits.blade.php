@extends('essentials::docs.layout')

@section('page', 'Traits')
@section('lead', 'Behaviour your models opt into. Essentials ships traits, not base models to extend.')

@section('content')
    <x-essentials::docs.section id="overview" title="The traits">
        <x-essentials::docs.table :columns="['Trait', 'Gives a model']" :rows="[
            ['HasUlids', 'A ULID key generated on create, used as the route key.'],
            ['HasMorphRouteKey', 'getMorphRouteKey(), formatted type:key, for polymorphic route binding.'],
            ['HasNumberIdentifiers', 'A year-scoped sequential number such as WOR-2026-0001.'],
            ['HasMoney', 'currencySymbol(), money() and relativeMoney() for amounts on the model.'],
            ['HasEnumElements', 'label(), lowerLabel() and random() for backed enums.'],
            ['HasUploads', 'File behaviour for the app Upload model: URL, size, extension and image check.'],
            ['HasProfileImage', 'A single replaceable profile image with an avatar fallback.'],
            ['HasKenyans', 'Realistic Kenyan names and safe fake phone numbers for factories.'],
            ['HasPermissions, HasUserPermissions, HasRoleList', 'Default roles and permissions from permissions.json.'],
        ]" />
    </x-essentials::docs.section>

    <x-essentials::docs.section id="ulids" title="Ulids">
        <x-essentials::docs.code>
            use Illuminate\Database\Eloquent\Model;
            use Nyumbani\Essentials\Traits\HasUlids;

            class Customer extends Model
            {
                use HasUlids;
            }
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="numbers" title="Number identifiers">
        <p>The number is built as prefix, year and a four digit sequence, and the sequence restarts each year. The prefix is the first three letters of the class name. Override <code>$numberPrefix</code> to choose your own.</p>
        <x-essentials::docs.code>
            use Illuminate\Database\Eloquent\Model;
            use Nyumbani\Essentials\Traits\HasNumberIdentifiers;

            class WorkOrder extends Model
            {
                use HasNumberIdentifiers;

                protected ?string $numberPrefix = 'WOR';
            }

            // WOR-2026-0001, WOR-2026-0002 ...
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="money" title="Money on a model">
        <x-essentials::docs.code>
            use Illuminate\Database\Eloquent\Model;
            use Nyumbani\Essentials\Traits\HasMoney;

            class Invoice extends Model
            {
                use HasMoney;
            }

            $invoice->money($invoice->total);                   // KES 1,500.00
            $invoice->money($invoice->total, currency: false);  // 1,500.00
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="enums" title="Enum elements">
        <x-essentials::docs.code>
            use Nyumbani\Essentials\Traits\HasEnumElements;

            enum InvoiceStatus: string
            {
                use HasEnumElements;

                case Paid = 'paid';
                case PartiallyPaid = 'partially_paid';
            }

            InvoiceStatus::PartiallyPaid->label();      // Partially Paid
            InvoiceStatus::random();
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="uploads" title="Uploads and profile images">
        <p>The app owns its <code>Upload</code> model and its <code>UploadType</code> enum. <code>HasUploads</code> adds the file behaviour, and <code>HasProfileImage</code> reads the newest upload typed <code>profile-image</code>, falling back to a generated avatar.</p>
        <x-essentials::docs.code>
            use Illuminate\Database\Eloquent\Model;
            use Illuminate\Foundation\Auth\User as Authenticatable;
            use Illuminate\Database\Eloquent\Relations\MorphMany;
            use Nyumbani\Essentials\Traits\HasProfileImage;
            use Nyumbani\Essentials\Traits\HasUlids;
            use Nyumbani\Essentials\Traits\HasUploads;

            class Upload extends Model
            {
                use HasUlids, HasUploads;
            }

            class User extends Authenticatable
            {
                use HasProfileImage;

                public function uploads(): MorphMany
                {
                    return $this->morphMany(Upload::class, 'uploadable');
                }
            }

            $user->hasProfileImage();
        </x-essentials::docs.code>
    </x-essentials::docs.section>
@endsection
