---
paths:
  - 'app/Services/Messaging/**'
---

# Messaging

## Send through `nyumbani/messaging`, never a provider
Dispatch `Nyumbani\Messaging\Events\MessageProcessed` with an `sms` payload. Do not call Connect, Bunicom or any SMS provider with `Http` from an app.

## One handler per message type
Build each message in a class that extends `Nyumbani\Messaging\MessageHandler`, with its inputs in the constructor and the text in `handle()`. Do not assemble message text in controllers, models or jobs. A variant of one message is another protected method with no required arguments, chosen with `handler($handler, 'reminder')`.

## Start messages from the Messenger when a project can switch SMS off
`Messenger::for($project)` (the project implements `MessageScope`) supplies the header, footer and sender ID, and answers `canSend()`. Check `canSend()` before building messages, and start each message with `$messenger->recipient($model)`. Use `Messenger::global()` only when there is no scope.

## The recipient needs a `phone_number`
`Message` reads `phone_number` from the recipient. A column with another name gets a `phoneNumber()` accessor. A number with fewer than nine digits is dropped silently.

## Sync or a worker, both send
On `QUEUE_CONNECTION=sync` the SMS is posted during the request and a Connect failure throws a `RequestException` to the caller, with no retry. On any other connection a queue worker must be running; it retries a failure up to three times. Do not tell a user "nothing is sent" without checking which connection is in use.

## Test with fakes, never Connect
Use `Queue::fake()` and `Queue::assertPushed(SendMessageJob::class)` to prove a message was queued, and `Http::fake()` with `(new SendMessageJob(SmsGateway::class, $payload))->handle()` to prove the request. Assert the text with `Message::make()->...->toSMS()['text']`.
