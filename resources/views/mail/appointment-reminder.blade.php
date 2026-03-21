@component('mail::message')
# Appointment Reminder

Dear {{ $appointment->patient->first_name }},

@if(($reminderType ?? 'upcoming') === 'tomorrow')
This is a reminder that your appointment with **HomeOptic** is **tomorrow**.
@else
This is a reminder of your upcoming appointment with **HomeOptic**.
@endif

@component('mail::table')
| | |
|:--|:--|
| **Date** | {{ $appointment->date->format('l, j F Y') }} |
| **Time** | {{ $appointment->start_time }} |
| **Type** | {{ $appointment->appointment_type->label() }} |
| **Diary** | {{ $appointment->diary->name }} |
@endcomponent

If you need to rearrange your appointment or have any questions, please get in touch with us directly.

Thank you,<br>
**HomeOptic**
@endcomponent
