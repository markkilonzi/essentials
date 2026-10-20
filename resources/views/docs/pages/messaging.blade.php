@extends('essentials::docs.layout')

@section('page', 'Messaging')
@section('lead', 'Build an SMS once and let the package queue it through Nyumbani Connect.')

@section('content')
    <x-essentials::docs.section id="how" title="How it works">
        <p>Every message goes out through Nyumbani Connect. You build the text with <code>Message</code>, put it in a <code>MessageProcessed</code> event, and the package queues it, limits the speed and retries it when Connect fails.</p>
        <p>Only SMS is available for now. A payload is keyed by channel, so <code>sms</code> is the only key read.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="env" title="Environment">
        <x-essentials::docs.code lang="bash" title=".env">
            NYUMBANI_CONNECT_BASE_URL=
            NYUMBANI_CONNECT_TOKEN=
            ESSENTIALS_SMS_ENABLED=true
            ESSENTIALS_SMS_SENDER_ID=NYUMBANI
        </x-essentials::docs.code>
        <p>These feed <code>essentials.messaging.connect</code>. <code>sms.enabled</code> switches SMS off app-wide, <code>sms.sender_id</code> is used when a message has none, and <code>sms.uri</code> is the Connect path. <code>messaging.rate_limit_per_second</code> caps how many queued requests go out each second, and <code>defaults.messaging</code> turns the wiring off.</p>
        <x-essentials::docs.callout type="warning">The token is a secret. Keep it in <code>.env</code> only, and run a queue worker or nothing is sent.</x-essentials::docs.callout>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="build" title="Build a message">
        <x-essentials::docs.code title="app/Services/Messaging/Handlers/CheckInOtpMessageHandler.php">
            use Nyumbani\Essentials\Services\Messaging\MessageHandler;

            class CheckInOtpMessageHandler extends MessageHandler
            {
                public function __construct(private readonly string $code) {}

                protected function handle(): void
                {
                    $this->message->text("Your code is {$this->code}.");
                }
            }
        </x-essentials::docs.code>
        <p>A handler takes its inputs in the constructor and fills <code>$this->message</code>. To build another version of the message, add a protected method and pass its name: <code>handler($handler, 'reminder')</code>. A method that does not exist, is private, is static, needs arguments or belongs to the base class throws an <code>InvalidArgumentException</code>. The builder also has <code>newLine()</code>, <code>action()</code>, <code>prepend()</code>, <code>append()</code> and <code>when()</code>, and the header, salutation and footer wrap the body.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="send" title="Send it">
        <x-essentials::docs.code title="Anywhere in the app">
            use App\Services\Messaging\Handlers\CheckInOtpMessageHandler;
            use Nyumbani\Essentials\Services\Messaging\Message;
            use Nyumbani\Essentials\Services\Messaging\MessageProcessed;

            $message = Message::make()
                ->salutation($user->name)
                ->handler(new CheckInOtpMessageHandler($code));

            MessageProcessed::dispatch(['sms' => [
                $message->toSMS($user, 'ACME'),
            ]], ['otp']);
        </x-essentials::docs.code>
        <p><code>toSMS()</code> takes the recipient model, which needs a <code>phone_number</code>, and an optional sender ID. It returns the payload item with <code>scope_type</code>, <code>scope_id</code>, <code>client_type</code>, <code>client_id</code>, <code>sender_id</code>, <code>number</code>, <code>text</code> and <code>meta_data</code>. Set the scope with <code>scope($model)</code> and add meta data with <code>metaData([...])</code>; <code>app_url</code> is always included.</p>
        <p>Numbers are turned into <code>254</code> plus the last nine digits, and a message without a usable number is dropped. Connect always receives a list of messages, even for one, and the package refuses an empty or non-list payload. Messages are grouped by <code>sender_id</code> (the config value when it is null) and sent in requests of up to 1000. The second argument of the dispatch holds the queue tags. The scope, client and meta data are passed on to Connect.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="gateways" title="Gateways">
        <p>Every channel extends the abstract <code>Gateway</code>, which cleans the messages, batches them and queues one <code>SendMessageJob</code> per batch. A gateway only supplies <code>normalise()</code> (clean one message, or return null to drop it), <code>format()</code> (the request body of a batch) and <code>deliver()</code> (send it, throwing on failure). It may override <code>batches()</code> to group messages first, as <code>SmsGateway</code> does by sender ID.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="failures" title="Failures">
        <p>A failed request throws, so the queue retries it up to 25 times, waiting 60, 90 then 120 seconds. When it gives up, it logs <code>Message sending failed</code> with the error.</p>
    </x-essentials::docs.section>
@endsection
