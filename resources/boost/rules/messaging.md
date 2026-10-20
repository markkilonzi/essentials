---
paths:
  - 'app/Services/Messaging/**'
---

# Messaging

## Send through `nyumbani/messaging`, never a provider
Dispatch `Nyumbani\Messaging\Events\MessageProcessed` with an `sms` payload. Do not call Connect, Bunicom or any SMS provider with `Http` from an app.

## One handler per message type
Build each message in a class that extends `Nyumbani\Messaging\MessageHandler`, with its inputs in the constructor and the text in `handle()`. Do not assemble message text in controllers, models or jobs.
