<style>
    .permission-search {
        max-width: 320px;
    }

    .module-card {
        border-radius: 10px;
    }

    .module-header {
        font-weight: 600;
        font-size: 16px;
    }

    .page-box {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
    }

    .sub-box {
        padding-left: 22px;
        border-left: 2px solid #e5e7eb;
        margin-top: 10px;
    }

    .hidden {
        display: none;
    }

    .toggle-actions {
        cursor: pointer;
        color: #0d6efd;
    }

    .rotate {
        transform: rotate(180deg);
        transition: 0.2s ease;
    }

    .dropdown {
        position: relative;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        width: 100%;
        background: #fff;
        border: 1px solid #ddd;
        max-height: 220px;
        overflow-y: auto;
        z-index: 10;
    }

    .dropdown-content a {
        display: block;
        padding: 8px 12px;
        color: #000;
        text-decoration: none;
    }

    .dropdown-content a:hover {
        background: #f1f1f1;
    }

    .show {
        display: block;
    }
</style>
<div class="pagetitle mb-4 d-flex justify-content-between align-items-center">
    <nav>
        <h1>Assign Permissions</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Assign Permissions</li>
        </ol>
    </nav>

    <?php 
    // if (PermissionMiddleware::hasPage('manage-permissions')): 
    ?>
        <a class="btn btn-dark" href="../superadmin/manage-permissions">
            <i class="bi bi-gear"></i> Manage Permissions
        </a>
    <?php 
// endif;  
?>
</div>

<section class="section">
<div class="container-fluid">

<form method="POST" action="../superadmin/assign-permissions">

    <!-- USER -->
    <div class="card mb-4">
        <div class="card-body">
            <label class="fw-semibold">Select User</label>

            <div class="dropdown">
                <input
                    id="employee_search"
                    class="form-control"
                    placeholder="Search employee"
                    onclick="employeeDropdown.classList.add('show')"
                    oninput="filterEmployees()"
                    autocomplete="off"
                >

                <div id="employeeDropdown" class="dropdown-content">
                    <?php foreach ($employees as $e): ?>
                        <a href="#" data-id="<?= htmlspecialchars($e['employee_id']) ?>"> <?= htmlspecialchars($e['employee_id'] . ' - ' . $e['full_name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <input type="hidden" name="employee_id" id="employee_id" required>
        </div>
    </div>

    <!-- SEARCH + GLOBAL -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <input
            id="permissionSearch"
            class="form-control permission-search"
            placeholder="Search modules"
        >

        <button
            type="button"
            id="globalToggleBtn"
            class="btn btn-outline-dark btn-sm"
            onclick="toggleAllPermissions()">
            Select All
        </button>
    </div>

    <!-- MODULES -->
    <?php foreach ($pagesByModule as $module => $modulePages):
        $moduleKey = md5($module); ?>

        <div class="card module-card module-section mb-4"
             data-module-name="<?= strtolower(htmlspecialchars($module)) ?>">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="module-header"><?= htmlspecialchars($module) ?></span>

                    <button type="button"
                            class="btn btn-sm btn-outline-dark"
                            data-module="<?= $moduleKey ?>"
                            data-selected="false"
                            onclick="toggleModulePermissions(this)">
                        Select All
                    </button>
                </div>

                <div class="row g-3">

                    <?php foreach ($modulePages as $page): ?>

                        <div class="col-md-4">
                            <div class="page-box">

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check fw-semibold">
                                        <input
                                            type="checkbox"
                                            class="form-check-input page-checkbox"
                                            name="assigned_pages[]"
                                            value="<?= (int)$page['id'] ?>"
                                            id="page<?= (int)$page['id'] ?>"
                                            data-page="<?= (int)$page['id'] ?>"
                                            data-module="<?= $moduleKey ?>"
                                        >
                                        <label for="page<?= (int)$page['id'] ?>">
                                            <?= htmlspecialchars($page['name']) ?>
                                        </label>
                                    </div>

                                    <?php if (!empty($subPermissions[$page['id']])): ?>
                                        <span class="toggle-actions"
                                              onclick="toggleSubs(<?= (int)$page['id'] ?>)">
                                            <i class="bi bi-chevron-down"
                                               id="chevron<?= (int)$page['id'] ?>"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($subPermissions[$page['id']])): ?>
                                    <div class="sub-box hidden"
                                         id="subs<?= (int)$page['id'] ?>">

                                        <?php foreach ($subPermissions[$page['id']] as $sp): ?>
                                            <div class="form-check small">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input sub-checkbox"
                                                    name="assigned_sub_permissions[]"
                                                    value="<?= (int)$sp['id'] ?>"
                                                    id="sub<?= (int)$sp['id'] ?>"
                                                    data-page="<?= (int)$page['id'] ?>"
                                                    data-module="<?= $moduleKey ?>"
                                                >
                                                <label for="sub<?= (int)$sp['id'] ?>">
                                                    <?= htmlspecialchars($sp['permission_name']) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
            </div>
        </div>

    <?php endforeach; ?>

    <div class="text-end mt-4">
        <button class="btn btn-dark">Save Permissions</button>
    </div>

</form>
</div>
</section>

<script>
const userPages = <?= json_encode($userPages, JSON_HEX_TAG) ?>;
const userSubs  = <?= json_encode($userSubs, JSON_HEX_TAG) ?>;

/* Employee search */
function filterEmployees() {
    const q = employee_search.value.toUpperCase();
    document.querySelectorAll('#employeeDropdown a').forEach(a => {
        a.style.display = a.textContent.toUpperCase().includes(q) ? '' : 'none';
    });
}

/* Load permissions */
document.querySelectorAll('#employeeDropdown a').forEach(a => {
    a.onclick = e => {
        e.preventDefault();
        const id = a.dataset.id;

        employee_search.value = a.textContent;
        employee_id.value = id;
        employeeDropdown.classList.remove('show');

        document.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);

        userPages[id]?.forEach(pid => {
            const p = document.getElementById('page' + pid);
            if (p) p.checked = true;
        });

        userSubs[id]?.forEach(sid => {
            const s = document.getElementById('sub' + sid);
            if (s) s.checked = true;
        });

        document.querySelectorAll('.page-checkbox').forEach(p => {
            const page = p.dataset.page;
            const hasSub = document.querySelectorAll(
                `.sub-checkbox[data-page="${page}"]:checked`
            ).length > 0;
            if (hasSub) p.checked = true;
        });
    };
});

/* Accordion */
function toggleSubs(id) {
    document.getElementById('subs' + id).classList.toggle('hidden');
    document.getElementById('chevron' + id).classList.toggle('rotate');
}

/* Page ↔ Sub sync */
document.querySelectorAll('.page-checkbox').forEach(p => {
    p.onchange = () => {
        if (!p.checked) {
            document.querySelectorAll(
                `.sub-checkbox[data-page="${p.dataset.page}"]`
            ).forEach(s => s.checked = false);
        }
    };
});

document.querySelectorAll('.sub-checkbox').forEach(s => {
    s.onchange = () => {
        const page = s.dataset.page;
        document.getElementById('page' + page).checked =
            document.querySelectorAll(
                `.sub-checkbox[data-page="${page}"]:checked`
            ).length > 0;
    };
});

/* Global toggle */
let globalSelected = false;
function toggleAllPermissions() {
    globalSelected = !globalSelected;
    document.querySelectorAll('.page-checkbox,.sub-checkbox')
        .forEach(cb => cb.checked = globalSelected);

    globalToggleBtn.textContent = globalSelected ? 'Unselect All' : 'Select All';
    globalToggleBtn.className = globalSelected
        ? 'btn btn-dark btn-sm'
        : 'btn btn-outline-dark btn-sm';
}

/* Module toggle */
function toggleModulePermissions(btn) {
    const key = btn.dataset.module;
    const selected = btn.dataset.selected === "true";

    document.querySelectorAll(`[data-module="${key}"]`)
        .forEach(cb => cb.checked = !selected);

    btn.dataset.selected = (!selected).toString();
    btn.textContent = !selected ? 'Unselect All' : 'Select All';
}

/* Module search */
permissionSearch.addEventListener('keyup', () => {
    const q = permissionSearch.value.toLowerCase().trim();
    document.querySelectorAll('.module-section').forEach(m => {
        m.style.display = !q || m.dataset.moduleName.includes(q) ? '' : 'none';
    });
});

/* Close dropdown */
document.addEventListener('click', e => {
    if (!employee_search.contains(e.target) &&
        !employeeDropdown.contains(e.target)) {
        employeeDropdown.classList.remove('show');
    }
});
</script>
