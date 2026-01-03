<?php
class NotificationRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function create(
        int $userId,
        string $title,
        string $message,
        ?string $url,
        string $type
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (user_id, title, message, related_url, type)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $title, $message, $url, $type]);
    }
    public function findUnreadByUser(int $userId, int $limit = 10): array
{
    $limit = (int) $limit; // 🔒 hard cast for safety

    $sql = "
        SELECT *
        FROM notifications
        WHERE user_id = ? AND is_read = 0 
        ORDER BY created_at DESC
        LIMIT $limit
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function markAsRead(int $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$id, $userId]);
        return $stmt->rowCount() > 0;
    }

    public function markAllAsRead(int $userId): int
    {
        $stmt = $this->pdo->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        return $stmt->rowCount();
    }

    public function unreadCount(int $userId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM notifications
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}
