@extends('essentials::docs.layout')

@section('page', 'Messaging')
@section('lead', 'SMS for every Nyumbani app: build a message once and let the package queue, limit and retry it.')

@section('content')
    <x-essentials::docs.section id="what" title="What it is">
        <p><code>nyumbani/messaging</code> sends SMS through Nyumbani Connect, so an app never talks to an SMS provider. Essentials requires it, so it is already installed in every Nyumbani app. It has its own docs, because it needs only Laravel and can be used on its own.</p>
        <x-essentials::docs.card title="Open the Messaging docs" href="{{ route('essentials.docs', ['package' => 'nyumbani-messaging']) }}">Installation, a walkthrough, handlers, the Messenger, gateways, and how to test and troubleshoot.</x-essentials::docs.card>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="offers" title="What it gives you">
        <x-essentials::docs.table :columns="['Piece', 'Use it to']" :rows="[
            ['Message', 'Build one SMS: recipient, salutation, body, header and footer.'],
            ['MessageHandler', 'Keep the wording of one kind of message in one class.'],
            ['MessageProcessed', 'Hand the finished messages to the queue.'],
            ['Messenger', 'Switch SMS off per project, and frame every message with its header, footer and sender.'],
            ['SendMessageJob', 'Deliver to Connect, rate limited and retried.'],
        ]" />
    </x-essentials::docs.section>

    <x-essentials::docs.section id="taste" title="A first taste">
        <x-essentials::docs.code>
            use Nyumbani\Messaging\Events\MessageProcessed;
            use Nyumbani\Messaging\Message;

            $sms = Message::make()
                ->recipient($user)
                ->salutation()
                ->text('Your rent is due on Friday.')
                ->toSMS();

            MessageProcessed::dispatch(['sms' => [$sms]]);
        </x-essentials::docs.code>
        <p>The recipient is a model with a <code>phone_number</code>. A queue worker must be running, because nothing is sent until it picks the job up. The Messaging docs let you build an SMS and see exactly what Connect would receive.</p>
    </x-essentials::docs.section>

    <x-essentials::docs.section id="agents" title="For AI agents">
        <p>The skill <code>nyumbani-messaging</code> and the shipped messaging rule load with the rest of Essentials. See <a href="{{ $docs->url('rules-and-skills') }}">Rules and skills</a>.</p>
    </x-essentials::docs.section>
@endsection
