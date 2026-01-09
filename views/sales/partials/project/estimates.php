<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-sm btn-dark" onclick="openCreateEstimateModal()">
        <i class="bi bi-plus-lg"></i> New Estimate
    </button>
</div>
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th class="ps-3">Ref</th>
                <th>Date</th>
                <th>Value</th>
                <th>Status</th>
                <th class="text-end pe-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($estimates)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No estimates found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($estimates as $e): ?>
                    <tr>
                        <td class="ps-3 fw-medium"><?= $e['quote_reference'] ?></td>
                        <td><?= date('d M Y', strtotime($e['proposal_date'])) ?></td>
                        <td>₹<?= number_format($e['project_cost']) ?></td>
                        <td>
                            <?php
                            $statusClass = match ($e['status']) {
                                'active' => 'success',
                                'booked' => 'primary',
                                'draft'  => 'secondary',
                                default  => 'warning'
                            };
                            ?>
                            <span class="badge bg-<?= $statusClass ?> rounded-pill px-2"><?= ucfirst($e['status']) ?></span>
                        </td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-primary border-0" onclick='viewEstimate(<?= json_encode($e) ?>)' title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Create Estimate Modal -->
<?php
ob_start();
?>
<input type="hidden" name="project_id" value="<?= $project['id'] ?>">
<div class="mb-3">
    <label class="form-label small">Proposal Date</label>
    <input type="date" class="form-control" name="proposal_date" value="<?= date('Y-m-d') ?>" required>
</div>
<div class="mb-3">
    <label class="form-label small">Project Value (₹)</label>
    <input type="number" class="form-control" name="project_cost" min="0" required>
</div>
<div class="mb-3">
    <label class="form-label small">Status</label>
    <select class="form-select" name="status">
        <option value="draft">Draft</option>
        <option value="active">Active</option>
    </select>
</div>
<?php
$modalBody = ob_get_clean();

ob_start();
?>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
<button type="submit" class="btn btn-dark btn-sm">Create Estimate</button>
<?php
$modalFooter = ob_get_clean();

$modalConfig = [
    'id' => 'createEstimateModal',
    'title' => 'New Estimate',
    'form' => [
        'action' => '',
        'method' => 'POST'
    ]
];
include __DIR__ . '/../../../components/modal.php';
?>

<!-- View Estimate Modal -->
<?php
ob_start();
?>
<div class="mb-3">
    <label class="text-muted small d-block">Value</label>
    <span class="fw-bold fs-5" id="viewEstValue"></span>
</div>
<div class="mb-3">
    <label class="text-muted small d-block">Date</label>
    <span class="fw-medium" id="viewEstDate"></span>
</div>
<div class="mb-3">
    <label class="text-muted small d-block">Status</label>
    <span class="badge" id="viewEstStatus"></span>
</div>
<?php
$modalBody = ob_get_clean();
$modalFooter = ''; // No footer for view
$modalConfig = [
    'id' => 'viewEstimateModal',
    'title' => 'Estimate Details' // default title, updated by JS
];
include __DIR__ . '/../../../components/modal.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if bootstrap is available
        if (typeof bootstrap !== 'undefined') {
            const createEstimateModal = new bootstrap.Modal(document.getElementById('createEstimateModal'));
            const viewEstimateModal = new bootstrap.Modal(document.getElementById('viewEstimateModal'));

            window.openCreateEstimateModal = function() {
                createEstimateModal.show();
            }

            window.viewEstimate = function(data) {
                document.getElementById('viewEstRef').innerText = data.quote_reference || 'Estimate Details';
                document.getElementById('viewEstValue').innerText = '₹' + new Intl.NumberFormat('en-IN').format(data.project_cost);
                document.getElementById('viewEstDate').innerText = new Date(data.proposal_date).toLocaleDateString('en-IN', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });

                const badge = document.getElementById('viewEstStatus');
                badge.innerText = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                badge.className = 'badge rounded-pill bg-' + (data.status === 'active' ? 'success' : (data.status === 'booked' ? 'primary' : 'secondary'));

                viewEstimateModal.show();
            }

            const createForm = document.querySelector('#createEstimateModal form');
            if (createForm) {
                createForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch('<?= BASE_PATH ?>/api/estimate/save', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Failed to create estimate');
                            }
                        });
                });
            }
        }
    });
</script>