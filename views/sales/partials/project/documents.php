<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-sm btn-primary" onclick="openUploadModal()"> <i class="bi bi-upload"></i> Upload</button>
</div>
<div class="row">
    <?php if (empty($documents)): ?>
        <div class="col-12 text-center text-muted py-4">No documents uploaded.</div>
    <?php else: ?>
        <?php foreach ($documents as $d): ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 border">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-file-earmark-text h1 text-primary mb-0 me-3"></i>
                            <div class="overflow-hidden">
                                <h6 class="mb-1 text-truncate" title="<?= $d['title'] ?>"><?= $d['title'] ?></h6>
                                <small class="text-muted"><?= date('d M Y', strtotime($d['created_at'])) ?></small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <a href="<?= BASE_PATH ?>/<?= $d['file_path'] ?>" target="_blank" class="btn btn-xs btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                            <button class="btn btn-xs btn-outline-danger" onclick="deleteDocument(<?= $d['id'] ?>)" title="Delete"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<?php
ob_start();
?>
<input type="hidden" name="project_id" value="<?= $project['id'] ?>">
<div class="mb-3">
    <label class="form-label small">Title</label>
    <input type="text" class="form-control" name="title" required>
</div>
<div class="mb-3">
    <label class="form-label small">File</label>
    <input type="file" class="form-control" name="file" required>
</div>
<?php
$modalBody = ob_get_clean();

ob_start();
?>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
<button type="submit" class="btn btn-primary">Upload</button>
<?php
$modalFooter = ob_get_clean();

$modalConfig = [
    'id' => 'uploadModal',
    'title' => 'Upload Document',
    'form' => [
        'action' => '',
        'method' => 'POST'
    ]
];
include __DIR__ . '/../../../components/modal.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof bootstrap !== 'undefined') {
            const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));

            window.openUploadModal = function() {
                uploadModal.show();
            }

            const uploadForm = document.querySelector('#uploadModal form');
            if (uploadForm) {
                uploadForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch('<?= BASE_PATH ?>/projects/documents/upload', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Upload failed');
                            }
                        });
                });
            }

            window.deleteDocument = function(id) {
                if (!confirm('Are you sure?')) return;

                const formData = new FormData();
                formData.append('id', id);

                fetch('<?= BASE_PATH ?>/projects/documents/delete', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Delete failed');
                        }
                    });
            }
        }
    });
</script>