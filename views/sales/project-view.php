<?php
// Calculate active estimates for badge
$activeEstimates = 0;
foreach ($estimates as $e) {
    if ($e['status'] == 'active' || $e['status'] == 'booked') {
        $activeEstimates++;
    }
}
?>

<div class="pagetitle mb-4">
    <h1>Project Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_PATH . '/dashboard' ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_PATH . '/projects' ?>">Projects</a></li>
            <li class="breadcrumb-item active"><?= $project['name'] ?></li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row g-4">

        <!-- Left Sidebar (Header + Nav + Info) -->
        <div class="col-lg-2">
            <div class="card border-0 shadow-sm ">
                <div class="card-body p-4">

                    <!-- Project Header Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h4 class="fw-bold text-dark mb-0"><?= $project['name'] ?></h4>
                            <button class="btn btn-sm btn-outline-light text-dark p-1 border-0 custom-hover-bg" style="margin-top: -5px; margin-right: -5px;" onclick="editProject('<?= $project['token'] ?>')" title="Edit Project">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>

                        <div class="mb-3">
                            <span class="badge bg-<?= $project['status'] === 'active' ? 'success' : 'secondary' ?> rounded-pill px-3 py-1">
                                <?= ucfirst($project['status']) ?>
                            </span>
                        </div>

                        <div class="text-muted small">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-geo-alt-fill me-2 text-danger opacity-75"></i>
                                <span><?= $project['city_name'] ?>, <?= $project['branch_name'] ?></span>
                            </div>
                            <?php if (!empty($project['client_id'])): ?>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-badge-fill me-2 text-primary opacity-75"></i>
                                    <span>Client: <span class="fw-medium text-dark"><?= $project['client_id'] ?></span></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25 my-4">

                    <!-- Navigation -->
                    <div class="nav flex-column nav-pills gap-2 mb-4" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active d-flex justify-content-between align-items-center text-start px-3 py-2" data-bs-toggle="pill" data-bs-target="#estimates" type="button" role="tab">
                            <span><i class="bi bi-receipt-cutoff me-2"></i> Estimates</span>
                            <?php if ($activeEstimates > 0): ?>
                                <span class="badge bg-primary rounded-pill"><?= $activeEstimates ?></span>
                            <?php endif; ?>
                        </button>
                        <button class="nav-link d-flex justify-content-between align-items-center text-start px-3 py-2" data-bs-toggle="pill" data-bs-target="#timeline" type="button" role="tab">
                            <span><i class="bi bi-calendar-event me-2"></i> Timeline</span>
                        </button>
                        <button class="nav-link d-flex justify-content-between align-items-center text-start px-3 py-2" data-bs-toggle="pill" data-bs-target="#documents" type="button" role="tab">
                            <span><i class="bi bi-folder2-open me-2"></i> Documents</span>
                        </button>
                    </div>

                    <hr class="text-muted opacity-25 my-4">

                    <!-- Team Section -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3 letter-spacing-1">Project Team</h6>

                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold small flex-shrink-0" style="width: 36px; height: 36px;">
                                <?= substr($project['rm_name'] ?? 'U', 0, 1) ?>
                            </div>
                            <div class="ms-3 overflow-hidden">
                                <div class="small fw-bold text-dark text-truncate"><?= $project['rm_name'] ?: 'No Manager' ?></div>
                                <div class="text-muted small" style="font-size: 0.75rem;">Relationship Manager</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold small flex-shrink-0" style="width: 36px; height: 36px;">
                                <?= substr($project['designer_name'] ?? 'U', 0, 1) ?>
                            </div>
                            <div class="ms-3 overflow-hidden">
                                <div class="small fw-bold text-dark text-truncate"><?= $project['designer_name'] ?: 'No Designer' ?></div>
                                <div class="text-muted small" style="font-size: 0.75rem;">Designer</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="avatar rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center fw-bold small flex-shrink-0" style="width: 36px; height: 36px;">
                                <?= substr($project['qc_name'] ?? 'U', 0, 1) ?>
                            </div>
                            <div class="ms-3 overflow-hidden">
                                <div class="small fw-bold text-dark text-truncate"><?= $project['qc_name'] ?: 'No QC Lead' ?></div>
                                <div class="text-muted small" style="font-size: 0.75rem;">QC Lead</div>
                            </div>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25 my-4">

                    <!-- Property Details -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3 letter-spacing-1">Overview</h6>
                        <div class="mb-2">
                            <span class="d-block text-muted small mb-1">Configuration</span>
                            <span class="d-block text-dark small fw-medium text-break">
                                <?= ucfirst($project['project_type'] ?? '') ?> • <?= ucfirst($project['house_type']) ?> (<?= $project['bhk_type'] ?>)
                            </span>
                        </div>
                        <div class="mb-2">
                            <span class="d-block text-muted small mb-1">Details</span>
                            <span class="d-block text-dark small fw-medium">
                                Flat: <?= $project['flat_no'] ?: '-' ?> • Floors: <?= $project['floors'] ?: '-' ?>
                            </span>
                        </div>
                        <div>
                            <span class="d-block text-muted small mb-1">Address</span>
                            <span class="d-block text-dark small fw-medium text-break">
                                <?= $project['project_address'] ?: 'No address provided' ?>
                            </span>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25 my-4">

                    <!-- Critical Dates -->
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold small mb-3 letter-spacing-1">Important Dates</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Booking</span>
                            <span class="text-dark small fw-medium"><?= $project['booking_date'] ? date('d M Y', strtotime($project['booking_date'])) : '-' ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Signoff</span>
                            <span class="text-dark small fw-medium"><?= $project['signoff_date'] ? date('d M Y', strtotime($project['signoff_date'])) : '-' ?></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm" style="min-height: calc(100vh);">
                <div class="card-body p-4">
                    <div class="tab-content" id="v-pills-tabContent">
                        <!-- Estimates Tab (Default) -->
                        <div class="tab-pane fade show active" id="estimates">
                            <?php include __DIR__ . '/partials/project/estimates.php'; ?>
                        </div>
                        <div class="tab-pane fade" id="timeline">
                            <?php include __DIR__ . '/partials/project/timeline.php'; ?>
                        </div>
                        <div class="tab-pane fade" id="documents">
                            <?php include __DIR__ . '/partials/project/documents.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Minimal Custom Overrides where Bootstrap isn't enough */
    .letter-spacing-1 {
        letter-spacing: 0.5px;
    }

    .custom-hover-bg:hover {
        background-color: #f8f9fa;
    }

    /* Nav Pill Active State Override */
    .nav-pills .nav-link {
        color: #6c757d;
        font-weight: 500;
        border-radius: 8px;
    }

    .nav-pills .nav-link:hover {
        background-color: #f8f9fa;
        color: #212529;
    }

    .nav-pills .nav-link.active {
        background-color: #eef2ff;
        color: #4154f1;
        font-weight: 600;
    }

    /* Scrollbar for sidebar card if content overflows */
    .card-body::-webkit-scrollbar {
        width: 4px;
    }

    .card-body::-webkit-scrollbar-thumb {
        background: #e9ecef;
        border-radius: 4px;
    }
</style>

<script>
    function editProject(token) {
        window.location.href = '<?= BASE_PATH ?>/projects?edit=' + token;
    }
</script>