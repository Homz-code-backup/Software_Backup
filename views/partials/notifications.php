<?php
$notificationRepo = new NotificationRepository();
$notificationService = new NotificationService($notificationRepo);

$notifications = $notificationService->getUnread($_SESSION['user_id']);
$count = $notificationService->unreadCount($_SESSION['user_id']);
?>

<div class="dropdown">
        <button class="btn btn-light position-relative" type="button" id="notificationDropdownBtn"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-bell-fill" style="color: #292a2b; font-size: 24px;"></i>
            <?php if ($count > 0): ?>
                <span class="notification-badge"><?= $count ?></span>
            <?php endif; ?>
        </button>

    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdownBtn"
        style="width: 320px; max-height: 400px; overflow-y: auto;">

        <li class="dropdown-header fw-bold">Notifications</li>

        <?php if ($count === 0): ?>
            <li class="dropdown-item text-muted text-center">
                No new notifications
            </li>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <li>
                    <a class="dropdown-item"
                        href="<?= BASE_PATH ?>/notifications/read?id=<?= $n['id'] ?>&go=<?= urlencode($n['related_url']) ?>">
                        <strong><?= htmlspecialchars($n['title']) ?></strong>
                        <div class="small text-muted">
                            <?= htmlspecialchars($n['message']) ?>
                        </div>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>