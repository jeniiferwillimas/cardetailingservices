<x-mail::message>
# Booking Confirmed

Hello **{{ $customerName }}**,

Thank you for booking with **{{ config('app.name') }}**! Your detailing appointment has been received.

---

**Order Reference:**
`{{ $orderReference }}`

---

## Appointment Details

| | |
|:-------------|:------|
| **Date & Time** | {{ $scheduledFor }} |
| **Location** | {{ $address }}, {{ $state }} |
@if($vehicleInfo)
| **Vehicle** | {{ $vehicleInfo }} |
@endif

---

## Services Booked

<x-mail::table>
| Service | Qty | Price |
|:--------|:---:|------:|
@foreach($serviceLines as $line)
| {{ $line['name'] }} | {{ $line['quantity'] }} | ${{ number_format($line['price'] * $line['quantity'], 2) }} |
@endforeach
| **Total** | | **${{ number_format($total, 2) }}** |
</x-mail::table>

---

<x-mail::panel>
**Next Step:** Complete your payment on the checkout page. Once confirmed, we will reach out to finalize your appointment details.
</x-mail::panel>

<x-mail::button :url="config('app.frontend_url', 'http://localhost:3000') . '/booking'" color="primary">
Complete Payment
</x-mail::button>

If you have any questions, reply to this email or reach us at **{{ config('mail.from.address') }}**.

Thanks,<br>
**{{ config('app.name') }}**
</x-mail::message>
