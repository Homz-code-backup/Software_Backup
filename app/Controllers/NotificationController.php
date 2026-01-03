<?php

class NotificationController
{
    private NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function read()
    {
        AuthMiddleware::check();
 
        $id = (int)($_GET['id'] ?? 0);
        $go = $_GET['go'] ?? '/dashboard';

        if ($id <= 0) {
            redirect('/dashboard');
        }

        $this->service->markAsRead($id, $_SESSION['user_id']);

        // Redirect to actual page
        redirect($go);
    }
}
