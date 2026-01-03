<?php
class NotificationService
{
     private NotificationRepository $repo;

    public function __construct()
    {
        $this->repo = new NotificationRepository();
    }

    public function notifyUser(
        int $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?string $url = null
    ): void {
        $this->repo->create(
            $userId,
            $title,
            $message,
            $type,
            $url
        );
    }

    public function getUnread(int $userId): array
    {
        return $this->repo->findUnreadByUser($userId);
    }

    public function markAsRead(int $id, int $userId): bool
    {
        return $this->repo->markAsRead($id, $userId);
    }

    public function unreadCount(int $userId): int
    {
        return $this->repo->unreadCount($userId);
    }
}
?>