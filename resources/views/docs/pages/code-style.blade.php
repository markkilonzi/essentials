@extends('docs::layout')

@section('page', 'Code style')
@section('lead', 'How PHP, JavaScript and tests are written: doc blocks, comments, names and imports.')

@section('content')
    <x-docs::section id="docblocks" title="Doc blocks">
        <p>These rules beat the generic Laravel Boost text. Where they disagree, the Nyumbani rule wins.</p>
        <x-docs::rule title="An author tag on every method">
            <p>Every PHP method has a doc block with a one-line description and an <code>@@author</code> tag. An existing author is never overwritten. When none is set, use the developer from <code>git config user.name</code> and <code>git config user.email</code>; if git has none, ask.</p>
        </x-docs::rule>
        <x-docs::rule title="Never param or return tags">
            <p>The type hints already say it. Put an array's shape in a <code>@@var</code> on the property, or in a short sentence in the summary.</p>
        </x-docs::rule>
        <x-docs::rule title="Document every property">
            <p>Every declared property gets a doc block with a one-line description and no <code>@@var</code>. Constructor-promoted properties are described by the constructor's doc block.</p>
        </x-docs::rule>
        <x-docs::rule title="Keep comments short">
            <p>A comment says what, in a short phrase. One line is the norm. If a reason is not obvious, give it in a few words, never a paragraph.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="comments" title="Comments">
        <x-docs::rule title="Narrate with //  comments">
            <p>Write a <code>//  </code> comment (two slashes and two spaces) above each logical step of a method body, in PHP and in JavaScript. A method whose body is a single statement needs no inline comment.</p>
            <x-docs::code lang="php" title="app/Services/InvoiceReminder.php">
                @verbatim
                use App\Models\Invoice;

                class InvoiceReminder
                {
                    /**
                     * Send a reminder for every overdue invoice
                     *
                     * @author Jane Doe <jane@example.com>
                     */
                    public function handle(): int
                    {
                        //  Find the invoices that are overdue
                        $invoices = Invoice::query()->where('due_at', '<', today())->get();

                        //  Remind each customer
                        $invoices->each(fn (Invoice $invoice) => $invoice->remind());

                        return $invoices->count();
                    }
                }
                @endverbatim
            </x-docs::code>

        </x-docs::rule>
        <x-docs::rule title="JavaScript header comments">
            <p>Every function, event listener and option object has a <code>/** ... */</code> comment above it with a one-line description, laid out over three lines. A function with exactly one top-level statement keeps the header and skips the inline comment. A function with two or more statements comments each one.</p>
            <x-docs::code lang="js" title="resources/js/example.js">
                @verbatim
                /**
                 * Add a keydown event listener to the document
                 */
                document.addEventListener('keydown', (event) => {
                    //  Close the menu on Escape
                    if (event.key === 'Escape') closeMenu();

                    //  Remember the last key
                    lastKey = event.key;
                });
                @endverbatim
            </x-docs::code>

        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="names" title="Names and imports">
        <x-docs::rule title="Descriptive names">
            <p>Use full words, never abbreviations. Boolean methods and properties start with <code>is</code>, <code>has</code>, <code>can</code> or <code>should</code>. Order class members with properties first, then methods.</p>
        </x-docs::rule>
        <x-docs::rule title="Import every class">
            <p>Always <code>use</code> a class and write its short name. Never write a fully-qualified name inline. In a view, import with <code>@@use('Vendor\Class')</code> at the top of the file. The same applies to every code sample in these docs.</p>
        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="tests" title="Tests">
        <x-docs::rule title="Pest, with a doc block on everything">
            <p>Write tests with Pest (<code>it()</code> and <code>test()</code> closures with <code>expect()</code>), not PHPUnit classes. Put a doc block before every test, hook (<code>beforeEach</code>, <code>afterEach</code>), helper function and fake class method: a one-line summary of what is proven, a blank line, then <code>@@author</code>.</p>
            <x-docs::code lang="php" title="tests/Feature/ReminderTest.php">
                @verbatim
                /**
                 * Reminders go to overdue invoices only
                 *
                 * @author Jane Doe <jane@example.com>
                 */
                it('reminds overdue invoices only', function () {
                    expect(true)->toBeTrue();
                });
                @endverbatim
            </x-docs::code>

        </x-docs::rule>
    </x-docs::section>

    <x-docs::section id="clean" title="Keep it lean">
        <x-docs::rule title="No dead code">
            <p>No empty stub methods, no commented-out methods or classes, and no routes pointing at them. Implement the action or delete it and any button that calls it.</p>
        </x-docs::rule>
        <x-docs::rule title="No DTO layer">
            <p>Do not add DTO or readonly value-object classes. Pass arrays, or use <code>fluent()</code> when a view wants <code>$data-&gt;property</code>.</p>
        </x-docs::rule>
    </x-docs::section>
@endsection
