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

        <form method="POST" action="../superadmin/assign-permissions" id="permissionsForm">

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
                            autocomplete="off">

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
                    placeholder="Search modules">

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
                                                    data-module="<?= $moduleKey ?>">
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
                                                            data-city-scope="<?= (int)($sp['requires_city_scope'] ?? 0) ?>"
                                                            data-branch-scope="<?= (int)($sp['requires_branch_scope'] ?? 0) ?>">
                                                        <label for="sub<?= (int)$sp['id'] ?>" class="<?= ($sp['requires_city_scope'] || $sp['requires_branch_scope']) ? 'scoped-label' : '' ?>" style="<?= ($sp['requires_city_scope'] || $sp['requires_branch_scope']) ? 'cursor:pointer;' : '' ?>">
                                                            <?= htmlspecialchars($sp['permission_name']) ?>
                                                            <?php if (!empty($sp['requires_city_scope'])): ?>
                                                                <i class="bi bi-geo-alt-fill text-info small" title="City Scope Required"></i>
                                                            <?php endif; ?>
                                                            <?php if (!empty($sp['requires_branch_scope'])): ?>
                                                                <i class="bi bi-building text-warning small" title="Branch Scope Required"></i>
                                                            <?php endif; ?>
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

    </div>
</section>

<!-- Load Permission Scope Modal -->
<div class="modal fade" id="loadPermissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select <span id="modalSubPermissionName" class="text-primary"></span> Scope</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label d-block fw-bold">Permission Type</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="load_scope_type" id="scopeAll" value="all" checked onchange="toggleScopeInputs()" form="permissionsForm">
                        <label class="form-check-label" for="scopeAll">All</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="load_scope_type" id="scopeCity" value="city" onchange="toggleScopeInputs()" form="permissionsForm">
                        <label class="form-check-label" for="scopeCity">City</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="load_scope_type" id="scopeBranch" value="branch" onchange="toggleScopeInputs()" form="permissionsForm">
                        <label class="form-check-label" for="scopeBranch">Branch</label>
                    </div>
                </div>

                <div id="citySelection" class="hidden border p-3 rounded" style="max-height: 250px; overflow-y: auto;">
                    <label class="fw-bold mb-2">Select Cities</label>
                    <?php foreach ($cities as $c): ?>
                        <div class="form-check">
                            <input class="form-check-input city-scope-checkbox" type="checkbox" name="selected_cities[]" value="<?= $c['id'] ?>" id="scope_city_<?= $c['id'] ?>" form="permissionsForm">
                            <label class="form-check-label" for="scope_city_<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="branchSelection" class="hidden border p-3 rounded" style="max-height: 250px; overflow-y: auto;">
                    <label class="fw-bold mb-2">Select Branches</label>
                    <?php foreach ($branches as $b): ?>
                        <div class="form-check">
                            <input class="form-check-input branch-scope-checkbox" type="checkbox" name="selected_branches[]" value="<?= $b['id'] ?>" id="scope_branch_<?= $b['id'] ?>" form="permissionsForm">
                            <label class="form-check-label" for="scope_branch_<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="confirmLoadScope()">Apply</button>
            </div>
        </div>
    </div>
</div>

</form>

<script>
    const userPages = <?= json_encode($userPages, JSON_HEX_TAG) ?>;
    const userSubs = <?= json_encode($userSubs, JSON_HEX_TAG) ?>;
    const employees = <?= json_encode($employees, JSON_HEX_TAG) ?>;
    const userCities = <?= json_encode($userCities, JSON_HEX_TAG) ?>;
    const userBranches = <?= json_encode($userBranches, JSON_HEX_TAG) ?>;
    let currentUserUserId = null;
    let activeScopedSubId = null;

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
            const employee = employees.find(emp => emp.employee_id === id);
            currentUserUserId = employee ? employee.user_id : null;

            employee_search.value = a.textContent;
            employee_id.value = id;
            employeeDropdown.classList.remove('show');

            // Reset all checkboxes
            document.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);

            userPages[id]?.forEach(pid => {
                const p = document.getElementById('page' + pid);
                if (p) p.checked = true;
            });

            userSubs[id]?.forEach(sid => {
                const s = document.getElementById('sub' + sid);
                if (s) s.checked = true;
            });

            // Pre-populate cities and branches
            if (currentUserUserId) {
                const cities = userCities[currentUserUserId] || [];
                const branches = userBranches[currentUserUserId] || [];

                if (cities.length > 0) {
                    document.getElementById('scopeCity').checked = true;
                    cities.forEach(cid => {
                        const cb = document.getElementById('scope_city_' + cid);
                        if (cb) cb.checked = true;
                    });
                } else if (branches.length > 0) {
                    document.getElementById('scopeBranch').checked = true;
                    branches.forEach(bid => {
                        const cb = document.getElementById('scope_branch_' + bid);
                        if (cb) cb.checked = true;
                    });
                } else {
                    document.getElementById('scopeAll').checked = true;
                }
                toggleScopeInputs();
            }

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
        const triggerModal = () => {
            const isCityScope = s.dataset.cityScope === '1';
            const isBranchScope = s.dataset.branchScope === '1';
            if (s.checked && (isCityScope || isBranchScope)) {
                const subName = s.nextElementSibling.textContent.trim();
                openLoadPermissionModal(parseInt(s.value), subName, isCityScope, isBranchScope);
            }
        };

        s.onchange = () => {
            const page = s.dataset.page;
            triggerModal();
            document.getElementById('page' + page).checked =
                document.querySelectorAll(`.sub-checkbox[data-page="${page}"]:checked`).length > 0;
        };

        // Special handling for labels of scoped permissions
        const label = s.nextElementSibling;
        if (label && label.classList.contains('scoped-label')) {
            label.addEventListener('click', (e) => {
                if (s.checked) {
                    e.preventDefault(); // Don't uncheck if already checked, just re-open modal
                    triggerModal();
                }
                // If unchecked, the 'for' behavior handles checking and triggers onchange
            });
        }
    });

    /* Load Permission Modal Logic */
    function openLoadPermissionModal(subId, subName, isCityScope, isBranchScope) {
        activeScopedSubId = subId;
        document.getElementById('modalSubPermissionName').textContent = subName;

        // Show/Hide radio options based on permission requirements
        document.getElementById('scopeCity').parentElement.style.display = isCityScope ? 'inline-block' : 'none';
        document.getElementById('scopeBranch').parentElement.style.display = isBranchScope ? 'inline-block' : 'none';

        // Reset modal UI based on current selections or requirements
        const hasCities = document.querySelectorAll('.city-scope-checkbox:checked').length > 0;
        const hasBranches = document.querySelectorAll('.branch-scope-checkbox:checked').length > 0;

        if (isCityScope && (hasCities || !isBranchScope)) {
            document.getElementById('scopeCity').checked = true;
        } else if (isBranchScope && (hasBranches || !isCityScope)) {
            document.getElementById('scopeBranch').checked = true;
        } else {
            document.getElementById('scopeAll').checked = true;
        }

        toggleScopeInputs();
        const modal = new bootstrap.Modal(document.getElementById('loadPermissionModal'));
        modal.show();
    }

    function toggleScopeInputs() {
        const scope = document.querySelector('input[name="load_scope_type"]:checked').value;
        document.getElementById('citySelection').classList.toggle('hidden', scope !== 'city');
        document.getElementById('branchSelection').classList.toggle('hidden', scope !== 'branch');

        if (scope === 'all') {
            document.querySelectorAll('.branch-scope-checkbox').forEach(cb => cb.checked = false);
            document.querySelectorAll('.city-scope-checkbox').forEach(cb => cb.checked = true);
        } else if (scope === 'city') {
            document.querySelectorAll('.branch-scope-checkbox').forEach(cb => cb.checked = false);
        } else if (scope === 'branch') {
            document.querySelectorAll('.city-scope-checkbox').forEach(cb => cb.checked = false);
        }
    }

    function confirmLoadScope() {
        const modalElement = document.getElementById('loadPermissionModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();
    }

    /* Global toggle */
    let globalSelected = false;

    function toggleAllPermissions() {
        globalSelected = !globalSelected;
        document.querySelectorAll('.page-checkbox,.sub-checkbox')
            .forEach(cb => cb.checked = globalSelected);

        globalToggleBtn.textContent = globalSelected ? 'Unselect All' : 'Select All';
        globalToggleBtn.className = globalSelected ?
            'btn btn-dark btn-sm' :
            'btn btn-outline-dark btn-sm';
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