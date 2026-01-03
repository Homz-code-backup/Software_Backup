<?php
$sno = $offset + 1;
foreach ($rows as $e): ?>
    <tr>
        <td class='col-sno'><?= $sno++ ?></td>
        <td class='col-id'><?= $e['id'] ?></td>
        <td class='col-employee_id'><?= $e['employee_id'] ?></td>
        <td class='col-full_name'><?= $e['full_name'] ?></td>
        <td class='col-official_email'><?= $e['official_email'] ?></td>
        <td class='col-job_position'><?= $e['job_position'] ?></td>
        <td class='col-department_name'><?= $e['department_name'] ?></td>
        <td class='col-branch_name'><?= $e['branch_name'] ?></td>
        <td class='col-city_name'><?= $e['city_name'] ?></td>
        <td>
            <div class='d-flex gap-1'>
                <a class='btn btn-link text-info p-0'
                    href="<?= BASE_PATH ?>/employees/view/<?= $e['employee_id'] ?>">
                    <i class='bi bi-eye'></i>
                </a>
                <?php if (in_array($e['user_status'], ['Active', 'Registered'])): ?>
                    <button class='btn btn-link text-danger p-0' onclick='statusUpdate("<?= $e['employee_id'] ?>", "Inactive")' title='Deactivate'>
                        <i class='bi bi-person-x-fill'></i>
                    </button>
                <?php else: ?>
                    <button class='btn btn-link text-success p-0' onclick='statusUpdate("<?= $e['employee_id'] ?>", "Active")' title='Reactivate'>
                        <i class='bi bi-person-check-fill'></i>
                    </button>
                <?php endif; ?>
            </div>
        </td>
    </tr>
<?php endforeach; ?>