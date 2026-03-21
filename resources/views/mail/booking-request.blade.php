<x-mail::message>
# New Booking Request

A new appointment request has been submitted via the HomeOptic website.

**Patient:** {{ $booking->patient_form_data['first_name'] ?? '' }} {{ $booking->patient_form_data['surname'] ?? '' }}
**Date:** {{ $booking->appointment_date->format('l j F Y') }}
**Time:** {{ substr($booking->appointment_time, 0, 5) }}
**Type:** {{ \App\Enums\AppointmentType::from($booking->appointment_type)->label() }}

@if($booking->reason)
**Reason:** {{ $booking->reason }}
@endif

@if($booking->examiner_notes)
**Examiner Notes:** {{ $booking->examiner_notes }}
@endif

**Address:** {{ $booking->patient_form_data['address_line_1'] ?? '' }}, {{ $booking->patient_form_data['town_city'] ?? '' }}, {{ $booking->patient_form_data['post_code'] ?? '' }}

**Customer email:** {{ $booking->customer_email }}

<x-mail::button :url="url('/admin/notifications')">
Review in Admin
</x-mail::button>

Thanks,
HomeOptic
</x-mail::message>
