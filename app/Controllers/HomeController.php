<?php
class HomeController
{
    public function index(): void
    {
        VisitorTracker::track('home');
        $content = Database::getContent();
        View::render('home/index', [
            'seoPage' => 'home',
            'pageTitle' => 'BronxHomeServices — Professional Home Cleaning Services',
            'pageDesc' => 'Professional, insured home cleaning services. Book online in 60 seconds. Satisfaction guaranteed.',
            'content' => $content,
        ]);
    }
}
