<div class="row">
    <div class="col-12">
        <?php if (empty($timeline)): ?>
            <div class="alert alert-info py-3">
                <i class="bi bi-info-circle me-2"></i> Timeline will be generated once the project budget is defined.
            </div>
        <?php else: ?>
            <div class="timeline-container">

                <?php
                $currentStage = 0;
                foreach ($timeline as $t):
                    if ($currentStage != $t['stage_number']):
                        $currentStage = $t['stage_number'];
                ?>
                        <div class="stage-header mt-4 mb-3">
                            <h6 class="fw-bold text-uppercase text-secondary border-bottom pb-2">
                                <?= $t['stage_label'] ?>
                            </h6>
                        </div>
                    <?php endif; ?>

                    <div class="timeline-item d-flex align-items-center mb-3 p-3 bg-light rounded hover-shadow-sm border-start border-4 <?= $t['status'] == 'completed' ? 'border-success' : 'border-primary' ?>">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark"><?= $t['step_name'] ?></div>
                            <div class="text-muted small">
                                <?php
                                $date = $t['rescheduled_date'] ?: $t['planned_date'];
                                $isRescheduled = !empty($t['rescheduled_date']);
                                ?>
                                <span class="<?= $isRescheduled ? 'text-warning fw-bold' : '' ?>">
                                    <i class="bi bi-calendar-event me-1"></i> <?= date('d M Y', strtotime($date)) ?>
                                </span>
                                <?php if ($isRescheduled): ?>
                                    <span class="text-decoration-line-through text-muted ms-2 small"><?= date('d M Y', strtotime($t['planned_date'])) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="action-btn">
                            <!-- Only allow rescheduling if permitted (assume Perms check or role) -->
                            <button class="btn btn-sm btn-outline-secondary" onclick="openRescheduleModal(<?= $t['id'] ?>, '<?= $date ?>', '<?= $t['step_name'] ?>')">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Reschedule Modal -->
<?php
ob_start();
?>
<input type="hidden" id="reschedule_step_id" name="id">
<div class="mb-3">
    <label class="form-label small">New Date</label>
    <input type="date" class="form-control" name="date" id="reschedule_date" required>
</div>
<?php
$modalBody = ob_get_clean();

ob_start();
?>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
<button type="submit" class="btn btn-primary btn-sm">Save Date</button>
<?php
$modalFooter = ob_get_clean();

$modalConfig = [
    'id' => 'rescheduleModal',
    'title' => 'Reschedule',
    'size' => 'modal-sm',
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
            const rescheduleModal = new bootstrap.Modal(document.getElementById('rescheduleModal'));

            window.openRescheduleModal = function(id, currentDate, name) {
                document.getElementById('reschedule_step_id').value = id;
                document.getElementById('reschedule_date').value = currentDate;
                rescheduleModal.show();
            }

            const rescheduleForm = document.querySelector('#rescheduleModal form');
            if (rescheduleForm) {
                rescheduleForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch('<?= BASE_PATH ?>/estimates/update-timeline-date', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Failed to update date');
                            }
                        });
                });
            }
        }
    });
</script>