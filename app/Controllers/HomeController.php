<?php
class HomeController
{
    public function index(): void
    {
        $content = Database::getContent();
        View::render('home/index', [
            'seoPage'   => 'home',
            'pageTitle' => 'Clean27 — Professional Home Cleaning Services',
            'pageDesc'  => 'Professional, insured home cleaning services. Book online in 60 seconds. Satisfaction guaranteed.',
            'content'   => $content,
        ]);
    }
}
