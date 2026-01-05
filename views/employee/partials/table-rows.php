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
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle p-1" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear"></i> Actions
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="<?= BASE_PATH ?>/employees/view/<?= $e['employee_id'] ?>">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </li>
                    <li>
                        <?php if (in_array($e['user_status'], ['Active', 'Registered'])): ?>
                            <button class="dropdown-item text-danger" onclick='statusUpdate("<?= $e['employee_id'] ?>", "Inactive")'>
                                <i class="bi bi-person-x-fill"></i> Deactivate
                            </button>
                        <?php else: ?>
                            <button class="dropdown-item text-success" onclick='statusUpdate("<?= $e['employee_id'] ?>", "Active")'>
                                <i class="bi bi-person-check-fill"></i> Reactivate
                            </button>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
<?php endforeach; ?>