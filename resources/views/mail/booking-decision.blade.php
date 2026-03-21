<x-mail::message>
@if($approved)
# Your Appointment is Confirmed

Dear {{ $booking->patient_form_data['first_name'] ?? 'Patient' }},

Your appointment has been confirmed for **{{ $booking->appointment_date->format('l j F Y') }}** at **{{ substr($booking->appointment_time, 0, 5) }}**.

We look forward to seeing you.

If you have any questions, please don't hesitate to contact us.
@else
# Regarding Your Appointment Request

Dear {{ $booking->patient_form_data['first_name'] ?? 'Patient' }},

Thank you for submitting an appointment request with HomeOptic.

Unfortunately we are unable to accommodate your appointment request at this time. Your personal information has not been stored on our system.

Please contact us directly if you would like to discuss alternative arrangements.
@endif

Thanks,
HomeOptic
</x-mail::message>
