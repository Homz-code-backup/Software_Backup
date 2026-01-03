<?php
$repo = new SidebarRepository();
$sidebarTree = $repo->getSidebarTree($_SESSION['user_employee_id']);


$module_icons = [
    'HR' => 'assets/icons/sidebar/hr_icon.png',
    'Sales' => 'assets/icons/sidebar/sales_icon.png',
    'My HR' => 'assets/icons/sidebar/assign.png',
    'Presales' => 'assets/icons/sidebar/presales_icon.png',
    'Management' => 'assets/icons/sidebar/management_icon.png',
    'Designers' => 'assets/icons/sidebar/designer_icon.png',
    'Marketing' => 'assets/icons/sidebar/marketing_icon.png',
    'CRM' => 'assets/icons/sidebar/crm_icon.png',
    'Operations' => 'assets/icons/sidebar/operations_icon.png',
    'Accounts' => 'assets/icons/sidebar/accounts_icon.png',
    'Purchase' => 'assets/icons/sidebar/purchase_icon.png',
    'IT' => 'assets/icons/sidebar/it_icon.png',
    'Architects' => 'assets/icons/sidebar/architect_icon.png',
    'Admin' => 'assets/icons/sidebar/admin_icon.png',
    'Super Admin Panel' => 'assets/icons/sidebar/superadmin_icon.png',
];
?>

<aside id="sidebar" class="sidebar d-flex flex-column justify-content-between">
    <ul class="sidebar-nav">

        <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_PATH ?>/dashboard" title="Dashboard">
                    <i class="bi bi-house-door"></i>
                </a>
            </li>

        <!-- Modules -->
        <?php foreach ($sidebarTree as $module => $pages): ?>
            <?php $module_icon = $module_icons[$module] ?? 'bi bi-folder'; ?>

            <li class="nav-item position-relative">
                <a class="nav-link sidebar-icon-link" href="#" title="<?= htmlspecialchars($module) ?>" data-bs-toggle="tooltip"
                        data-bs-placement="right">
                    <?php if (str_ends_with($module_icon, '.png')): ?>
                        <img src="<?= BASE_PATH ?>/public/<?= $module_icon ?>" style="width:25px">
                    <?php else: ?>
                        <i class="<?= $module_icon ?>"></i>
                    <?php endif; ?>
                </a>

                <!-- Flyout Pages -->
                <ul class="submenu flyout">
                    <?php foreach ($pages as $page): ?>
                        <li>
                            <a href="<?= BASE_PATH ?><?= $page['link']; ?>" title="<?= $page['name']; ?>">
                                <i class="<?= $page['icon']; ?>" style="font-size:25px;padding-right:15px;"></i>
                                <?= $page['name']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>

    </ul>

    <!-- Bottom -->
    <ul class="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link" title="Settings">
                <i class="bi bi-gear-fill"></i>
            </a>
        </li>
    </ul>
</aside>
