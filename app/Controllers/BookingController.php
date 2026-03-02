<?php
class BookingController
{
    public function index(): void
    {
        VisitorTracker::track('booking');
        View::render('booking/index', [
            'seoPage' => 'booking',
            'pageTitle' => 'Book a Cleaning — BronxHomeServices',
            'pageDesc' => 'Book a professional home cleaning online in seconds.',
        ]);
    }

    public function confirmation(): void
    {
        // Params come from booking.js redirect after a successful API response
        $bookingId = htmlspecialchars($_GET['booking_id'] ?? '');
        $serviceName = htmlspecialchars($_GET['service'] ?? 'Home Cleaning');
        $date = htmlspecialchars($_GET['date'] ?? '');
        $total = htmlspecialchars($_GET['total'] ?? '');
        $firstName = htmlspecialchars($_GET['first_name'] ?? '');

        if (!$bookingId) {
            header('Location: ' . View::url('/'));
            exit;
        }

        View::render('booking/confirmation', [
            'seoPage' => 'booking',
            'pageTitle' => 'Booking Confirmed — BronxHomeServices',
            'pageDesc' => 'Your home cleaning is confirmed. See you soon!',
            'bookingId' => $bookingId,
            'serviceName' => $serviceName,
            'date' => $date,
            'total' => $total,
            'firstName' => $firstName,
        ]);
    }
}
