<style>
.module-card { border-radius:12px }
.page-card { border:1px solid #e5e7eb; border-radius:10px }
.sub-card { border:1px dashed #ced4da; border-radius:8px; padding:8px 10px; background:#f8f9fa }
code { font-size:.85em }
.sub-toggle { cursor:pointer; font-size:13px }
.sub-body { display:none }
.sub-toggle.open i { transform:rotate(180deg) }
</style>
<div class="pagetitle mb-4">
    <h1>Manage Permissions</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../superadmin/assign-permissions">Assign Permissions</a></li>
            <li class="breadcrumb-item active">Manage Permissions</li>
        </ol>
    </nav>
</div>
<section class="section">
<div class="container-fluid">

<div class="d-flex justify-content-between mb-3">
    <input id="moduleSearch" class="form-control" placeholder="Search module..." style="max-width:300px">
    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addPageModal">+ Add Page</button>
</div>

<?php foreach ($groupedPages as $module => $pages): ?>
<div class="card module-card mb-4 module-section" data-name="<?= strtolower($module) ?>">
    <div class="card-header fw-semibold"><?= htmlspecialchars($module) ?></div>
    <div class="card-body">
    <div class="row g-3">

    <?php foreach ($pages as $page): ?>
    <div class="col-md-6 col-lg-4">
    <div class="card page-card h-100">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center  mb-2">
            <div>
                <h6><i class="<?= $page['icon'] ?>"></i> <?= htmlspecialchars($page['name']) ?></h6>
                <code><?= htmlspecialchars($page['slug']) ?></code>
            </div>
            <div class="btn-group gap-1">
                <button class="btn btn-sm btn-outline-warning edit-page"
                    data-bs-toggle="modal" data-bs-target="#editPageModal"
                    data-id="<?= $page['id'] ?>"
                    data-name="<?= htmlspecialchars($page['name']) ?>"
                    data-slug="<?= htmlspecialchars($page['slug']) ?>"
                    data-link="<?= htmlspecialchars($page['link']) ?>"
                    data-icon="<?= htmlspecialchars($page['icon']) ?>"
                    data-module="<?= htmlspecialchars($page['module']) ?>">
                    <i class="bi bi-pencil"></i>
                </button>

                <form method="POST" action="../superadmin/manage-permissions/page">
                    <input type="hidden" name="id" value="<?= $page['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" name="delete_page" onclick="return confirm('Delete this permission page?')">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- SUB PERMISSIONS -->
        <div class="sub-toggle text-end mb-2" onclick="toggleSubs(<?= $page['id'] ?>)">
            <strong>Sub Permissions</strong> <i class="bi bi-chevron-down"></i>
        </div>

        <div class="sub-body" id="subs<?= $page['id'] ?>">
            <div class="text-end mb-2">
                <button class="btn btn-sm btn-dark"
                    data-bs-toggle="modal"
                    data-bs-target="#addSubModal"
                    data-page="<?= $page['id'] ?>">+ Add</button>
            </div>
            <div class="row">
                    <?php if (empty($page['sub_permissions'])): ?>
                        <p>No sub permissions found.</p>
                    <?php else: ?>
                            <?php foreach ($page['sub_permissions'] as $sp): ?>
                        <div class="col-md-6">
                            <div class="sub-card d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <div class="fw-semibold small"><?= $sp['permission_name'] ?></div>
                                    <code><?= $sp['slug'] ?></code>
                                </div>
                                <div class="btn-group gap-1">
                                    <button class="btn btn-sm btn-outline-warning edit-sub"
                                        data-bs-toggle="modal" data-bs-target="#editSubModal"
                                        data-id="<?= $sp['id'] ?>"
                                        data-name="<?= $sp['permission_name'] ?>"
                                        data-slug="<?= $sp['slug'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="../superadmin/manage-permissions/sub">
                                        <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" name="delete_sub_permission" onclick="return confirm('Delete this permission?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                            <?php endforeach; ?>
                    <?php endif; ?>
            </div>
        </div>

    </div>
    </div>
    </div>
    <?php endforeach; ?>

    </div>
    </div>
</div>
<?php endforeach; ?>

</div>
</section>
<!-- ADD PAGE -->
<div class="modal fade" id="addPageModal">
<div class="modal-dialog">
<form method="POST" action="../superadmin/manage-permissions/page" class="modal-content">
<div class="modal-header"><h5>Add Page</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<input class="form-control mb-2" name="name" placeholder="Page name" required>
<select class="form-select mb-2" name="module" required>
<option disabled selected>-- Module --</option>
<?php foreach ($modules as $m): ?><option><?= $m ?></option><?php endforeach; ?>
</select>
<input class="form-control mb-2" name="slug" placeholder="slug" required>
<input class="form-control mb-2" name="link" placeholder="/path" required>
<input class="form-control" name="icon" placeholder="bi bi-gear" required>
</div>
<div class="modal-footer"><button class="btn btn-dark" name="add_page">Add</button></div>
</form>
</div>
</div>

<!-- EDIT PAGE -->
<div class="modal fade" id="editPageModal">
<div class="modal-dialog">
<form method="POST" action="../superadmin/manage-permissions/page" class="modal-content">
<div class="modal-header"><h5>Edit Page</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<input type="hidden" name="id" id="ep_id">
<input class="form-control mb-2" name="name" id="ep_name" required>
<input class="form-control mb-2" name="slug" id="ep_slug" required>
<input class="form-control mb-2" name="link" id="ep_link" required>
<input class="form-control" name="icon" id="ep_icon" required>
</div>
<div class="modal-footer"><button class="btn btn-primary" name="edit_page">Save</button></div>
</form>
</div>
</div>

<!-- ADD SUB -->
<div class="modal fade" id="addSubModal">
<div class="modal-dialog">
<form method="POST" action="../superadmin/manage-permissions/sub" class="modal-content">
<div class="modal-header"><h5>Add Sub Permission</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<input type="hidden" name="page_id" id="as_page">
<input class="form-control mb-2" name="permission_name" placeholder="Name" required>
<input class="form-control" name="slug" placeholder="view | edit" required>
</div>
<div class="modal-footer"><button class="btn btn-dark" name="add_sub_permission">Save</button></div>
</form>
</div>
</div>

<!-- EDIT SUB -->
<div class="modal fade" id="editSubModal">
<div class="modal-dialog">
<form method="POST" action="../superadmin/manage-permissions/sub" class="modal-content">
<div class="modal-header"><h5>Edit Sub Permission</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<input type="hidden" name="id" id="es_id">
<input class="form-control mb-2" name="permission_name" id="es_name" required>
<input class="form-control" name="slug" id="es_slug" required>
</div>
<div class="modal-footer"><button class="btn btn-primary" name="edit_sub_permission">Update</button></div>
</form>
</div>
</div>

<script>
/* Module search */
moduleSearch.onkeyup = () => {
    document.querySelectorAll('.module-section').forEach(m => {
        m.style.display = m.dataset.name.includes(moduleSearch.value.toLowerCase()) ? '' : 'none';
    });
};

/* Toggle sub permissions */
function toggleSubs(id){
    const el = document.getElementById('subs'+id);
    el.style.display = el.style.display === 'block' ? 'none' : 'block';
}

/* Fill modals */
document.querySelectorAll('.edit-page').forEach(b => b.onclick = () => {
    ep_id.value = b.dataset.id;
    ep_name.value = b.dataset.name;
    ep_slug.value = b.dataset.slug;
    ep_link.value = b.dataset.link;
    ep_icon.value = b.dataset.icon;
});

document.querySelectorAll('.edit-sub').forEach(b => b.onclick = () => {
    es_id.value = b.dataset.id;
    es_name.value = b.dataset.name;
    es_slug.value = b.dataset.slug;
});

document.getElementById('addSubModal').addEventListener('show.bs.modal', e => {
    as_page.value = e.relatedTarget.dataset.page;
});
</script>
