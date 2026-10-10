<x-mail::message>
# New Chat Message

You have a new message from a customer on **{{ config('app.name') }}**.

---

| | |
|:-------------|:------|
| **From** | {{ $customerName ?? 'Anonymous' }} |
@if($customerEmail)
| **Email** | {{ $customerEmail }} |
@endif

---

## Message

<x-mail::panel>
{{ $messageBody }}
</x-mail::panel>

<x-mail::button :url="config('app.frontend_url', 'http://localhost:3000') . '/admin/chat'" color="primary">
Reply in Dashboard
</x-mail::button>

Thanks,<br>
**{{ config('app.name') }}**
</x-mail::message>
