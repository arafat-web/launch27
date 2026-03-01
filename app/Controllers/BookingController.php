<?php
class BookingController
{
    public function index(): void
    {
        View::render('booking/index', [
            'seoPage'   => 'booking',
            'pageTitle' => 'Book a Cleaning — Clean27',
            'pageDesc'  => 'Book a professional home cleaning online in seconds.',
        ]);
    }
}
