<?php

return [
    'auto_process_overdue_customer_bookings' => env('BOOKING_AUTO_PROCESS_OVERDUE_CUSTOMER_BOOKINGS', true),
    'customer_arrival_deadline' => env('BOOKING_CUSTOMER_ARRIVAL_DEADLINE', '23:00'),
    'customer_arrival_warning_minutes' => env('BOOKING_CUSTOMER_ARRIVAL_WARNING_MINUTES', 180),
];
