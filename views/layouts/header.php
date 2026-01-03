<header id="header" class="header d-flex align-items-center p-3">
    <div class="d-flex align-items-center justify-content-between">
        <i class="bi bi-list toggle-sidebar-btn"></i>
        <a href="<?= BASE_PATH ?>/dashboard">
            <img src="<?= BASE_PATH ?>/public/assets/img/logo/hzi.png" alt="Logo" class="img-fluid d-block" style="height: 40px;" id="logo_superadmin">
        </a>
    </div>
    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center"><li class="nav-item d-flex align-items-center pe-3">
                 <?php include __DIR__ . '/../partials/notifications.php'; ?>
            </li>
            <li class="nav-item d-flex align-items-center pe-3"><i class="bi bi-person" style="color: #292a2b; font-size: 24px;"></i><?= $_SESSION['employee_name'] ?></li>   
            <li class="nav-item d-flex align-items-center px-2">
                <a href="<?= BASE_PATH ?>/logout" title="Logout">
                    <i class="bi bi-box-arrow-right" style="color: #292a2b; font-size: 30px; font-weight: bold;"></i>
                </a>
            </li>         
        </ul>
    </nav>
</header>

<?php displayFlash(); ?>
