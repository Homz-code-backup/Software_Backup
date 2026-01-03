<?php

class DashboardController
{
    public function index()
    {
        AuthMiddleware::check();

        $data = ['title' => 'ERP-Dashboard'];
        view('index', $data, 'app');
    }
}
