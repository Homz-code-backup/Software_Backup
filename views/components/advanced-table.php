<?php
$normalizedColumns = [];
$initialHidden = [];
foreach ($tableConfig['columns'] as $key => $val) {
    if (is_array($val)) {
        $normalizedColumns[$key] = [
            'label' => $val['label'] ?? '',
            'visible' => $val['visible'] ?? true,
            'locked' => $val['locked'] ?? false,
        ];
    } else {
        $normalizedColumns[$key] = [
            'label' => $val,
            'visible' => true,
            'locked' => false,
        ];
    }
    if (!$normalizedColumns[$key]['visible']) {
        $initialHidden[] = $key;
    }
}
?>
<div class="card mb-3">
    <div class="d-flex justify-content-between align-items-center px-3 ">
        <h5 class="card-title">Filters</h5>
    </div>

    <div class="card-body">
        <div class="row flex-lg-row flex-md-row flex-column g-2 align-items-end">


            <!-- Search -->
            <div class="col">
                <input
                    class="form-control"
                    placeholder="<?= $tableConfig['search']['placeholder'] ?>"
                    oninput="erpTable.search('<?= $tableConfig['id'] ?>', this.value)">
            </div>

            <!-- Dynamic Filters -->
            <?php foreach ($tableConfig['filters'] as $f): ?>
                <div class="col">
                    <select
                        class="form-select"
                        id="filter_<?= $f['key'] ?>"
                        onchange="erpTable.filter('<?= $tableConfig['id'] ?>','<?= $f['key'] ?>',this.value)">
                        <option value="">All <?= $f['label'] ?></option>
                        <?php foreach ($f['options'] as $o): ?>
                            <option value="<?= $o['value'] ?>"
                                <?php if (!empty($o['dataset'])): ?>
                                <?php foreach ($o['dataset'] as $dKey => $dVal): ?>
                                data-<?= $dKey ?>="<?= $dVal ?>"
                                <?php endforeach; ?>
                                <?php endif; ?>><?= $o['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endforeach; ?>

            <!-- Rows per page -->

            <!-- Column selector -->
            <div class="col-md-1 text-md-end">
                <div class="dropdown w-100 w-md-auto">
                    <button
                        class="btn btn-outline-secondary dropdown-toggle w-100 w-md-auto"
                        type="button"
                        data-bs-toggle="dropdown">
                        Columns
                    </button>

                    <ul class="dropdown-menu p-2">
                        <?php foreach ($normalizedColumns as $key => $col): ?>
                            <li>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        <?= $col['visible'] ? 'checked' : '' ?>
                                        <?= $col['locked'] ? 'disabled' : '' ?>
                                        onchange="erpTable.toggleColumn('<?= $tableConfig['id'] ?>', '<?= $key ?>')"
                                        id="col_toggle_<?= $key ?>">
                                    <label class="form-check-label" for="col_toggle_<?= $key ?>">
                                        <?= $col['label'] ?>
                                    </label>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="card" style="overflow: scroll;">

    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title"><?= $tableConfig['title'] ?></h5>
        </div>
        <div class="col-6 col-md-1 col-lg-1">
            <select
                class="form-select "
                onchange="erpTable.setLimit('<?= $tableConfig['id'] ?>', this.value)">
                <option value="10">10 Rows</option>
                <option value="25">25 Rows</option>
                <option value="50">50 Rows</option>
                <option value="100">100 Rows</option>
            </select>
        </div>
    </div>

    <div class="card-body">

        <table class="table table-striped" id="<?= $tableConfig['id'] ?>">
            <thead>
                <tr>
                    <?php foreach ($normalizedColumns as $key => $col): ?>
                        <th class="col-<?= $key ?>"><?= $col['label'] ?></th>
                    <?php endforeach; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <nav>
            <p></p>
            <ul class="pagination justify-content-end"
                id="<?= $tableConfig['id'] ?>_pagination"></ul>
        </nav>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        erpTable.init(
            '<?= $tableConfig['id'] ?>',
            '<?= $tableConfig['endpoint'] ?>',
            <?= json_encode($tableConfig['search']['fields']) ?>,
            <?= json_encode($initialHidden) ?>,
            <?= json_encode($tableConfig['initialFilters'] ?? (object)[]) ?>
        );
    });
</script>