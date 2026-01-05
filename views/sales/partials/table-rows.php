<?php
$sno = $offset + 1;
foreach ($rows as $pc): ?>
    <tr>
        <td class='col-sno'><?= $sno++ ?></td>
        <td class='col-id'><?= $pc['project_reference'] ?></td>
        <td class='col-name'>
            <a class='btn btn-link text-black p-0' href="<?= BASE_PATH ?>/sales/view/<?= $pc['id'] ?>">
                <?= $pc['name'] ?>
            </a>
        </td>
        <td class='col-email'><?= $pc['email'] ?></td>
        <td class='col-project_name'><?= $pc['project_name'] ?></td>
        <td class='col-contact_no'><?= $pc['contact_no'] ?></td>
        <td class='col-branch_name'><?= $pc['branch_name'] ?></td>
        <td class='col-city_name'><?= $pc['city_name'] ?></td>
        <td class='col-status'><?= $pc['status'] ?></td>
        <td>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle p-1" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear"></i> Actions
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="<?= BASE_PATH ?>/sales/view/<?= $pc['id'] ?>">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= BASE_PATH ?>/sales/edit/<?= $pc['id'] ?>">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr> 
<?php endforeach; ?>