@component('mail::message')
# Withdrawal Request Received

We have received your withdrawal request.
Please be patient as we look into your
request for confirmation and approval.

{{-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent --}}

Thanks for choosing Dotori,<br>
{{ config('app.name') }}
@endcomponent
