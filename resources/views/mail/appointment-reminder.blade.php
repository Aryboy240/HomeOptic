@component('mail::message')
# Appointment Reminder

Dear {{ $appointment->patient->first_name }},

This is a reminder of your upcoming appointment with **HomeOptic**.

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
