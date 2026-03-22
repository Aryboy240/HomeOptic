<x-mail::message>
# Action Required — Unreviewed Booking Request (24h)

A booking request submitted 24 hours ago has not yet been reviewed. **It will be automatically declined in approximately 24 hours if no action is taken.**

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

**Customer email:** {{ $booking->customer_email }}

<x-mail::button :url="url('/admin/notifications')" color="red">
Review Now
</x-mail::button>

Thanks,
HomeOptic
</x-mail::message>
