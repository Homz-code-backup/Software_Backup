<h1>Dashboard  </h1>

<div class="dashboard-cards">

    <?php if (PermissionService::hasPage('estimate')): ?>
        <div class="card">
            <p>Estimates</p>
            <?php if (PermissionService::hasAction('estimate.add')): ?>
                <a href="<?= BASE_PATH ?>/estimate/add">Add Estimate</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (PermissionService::hasPage('edit_attendance')): ?>
        <div class="card">
            <p>Attendance</p>
            <?php if (PermissionService::hasAction('attendance.add')): ?>
                <a href="<?= BASE_PATH ?>/attendance/add">Add Attendance</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
