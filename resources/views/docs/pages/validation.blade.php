@extends('essentials::docs.layout')

@section('page', 'Validation')
@section('lead', 'Rules for Kenyan numbers, a cast that cleans names, and error bags for forms in modals.')

@section('content')
    <x-essentials::docs.section id="form-requests" title="Use Form Requests">
        <p>Validate in a Form Request. Keep rules in named variables and give friendly names with <code>attributes()</code>.</p>
        <x-essentials::docs.code title="app/Http/Requests/StoreCustomerRequest.php">
            use Illuminate\Foundation\Http\FormRequest;
            use Nyumbani\Essentials\Rules\KenyanNumberValid;

            class StoreCustomerRequest extends FormRequest
            {
                public function rules(): array
                {
                    $name = ['required', 'string', 'max:255'];
                    $phone = ['required', new KenyanNumberValid];

                    return ['name' => $name, 'phone' => $phone];
                }

                public function attributes(): array
                {
                    return ['phone' => 'phone number'];
                }
            }
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="rules" title="Rules">
        <x-essentials::docs.table :columns="['Rule', 'Accepts']" :rows="[
            ['KenyanNumberValid', 'A Kenyan mobile number such as 0712345678, 254712345678 or +254712345678.'],
            ['PhoneNumberValid', 'An international number of 7 to 15 digits, with an optional leading +.'],
        ]" />
    </x-essentials::docs.section>

    <x-essentials::docs.section id="clean-name" title="CleanName cast">
        <p>Collapses repeated spaces and title-cases a name on the way in. It never changes what is read back.</p>
        <x-essentials::docs.code>
            use Illuminate\Database\Eloquent\Model;
            use Nyumbani\Essentials\Casts\CleanName;

            protected function casts(): array
            {
                return ['name' => CleanName::class];
            }
        </x-essentials::docs.code>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="error-bags" title="Error bags">
        <p>When several forms share a page, each needs its own errors. See <a href="{{ $docs->url('error-bags') }}">Error bags</a> for the <code>@@bag</code> directive.</p>
    </x-essentials::docs.section>
@endsection
