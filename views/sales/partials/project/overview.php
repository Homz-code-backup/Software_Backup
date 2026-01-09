<h5 class="card-title">Property Details</h5>
<div class="row">
    <div class="col-lg-3 col-md-4 label text-muted">Type</div>
    <div class="col-lg-9 col-md-8"><?= ucfirst($project['project_type'] ?? '') ?> - <?= ucfirst($project['house_type']) ?> (<?= $project['bhk_type'] ?>)</div>
</div>
<div class="row mt-2">
    <div class="col-lg-3 col-md-4 label text-muted">Details</div>
    <div class="col-lg-9 col-md-8">Flat: <?= $project['flat_no'] ?: '-' ?>, Floors: <?= $project['floors'] ?: '-' ?></div>
</div>
<div class="row mt-2">
    <div class="col-lg-3 col-md-4 label text-muted">Address</div>
    <div class="col-lg-9 col-md-8"><?= $project['project_address'] ?: '-' ?></div>
</div>
<div class="row mt-2">
    <div class="col-lg-3 col-md-4 label text-muted">Location</div>
    <div class="col-lg-9 col-md-8"><?= $project['city_name'] ?>, <?= $project['branch_name'] ?></div>
</div>

<h5 class="card-title mt-4">Project Team</h5>
<div class="row">
    <div class="col-lg-3 col-md-4 label text-muted">Manager</div>
    <div class="col-lg-9 col-md-8"><?= $project['rm_name'] ?: 'Not Assigned' ?></div>
</div>
<div class="row mt-2">
    <div class="col-lg-3 col-md-4 label text-muted">Designer</div>
    <div class="col-lg-9 col-md-8"><?= $project['designer_name'] ?: 'Not Assigned' ?></div>
</div>
<div class="row mt-2">
    <div class="col-lg-3 col-md-4 label text-muted">QC Lead</div>
    <div class="col-lg-9 col-md-8"><?= $project['qc_name'] ?: 'Not Assigned' ?></div>
</div>

<h5 class="card-title mt-4">Critical Dates</h5>
<div class="row">
    <div class="col-lg-3 col-md-4 label text-muted">Booking Date</div>
    <div class="col-lg-9 col-md-8"><?= $project['booking_date'] ? date('d M Y', strtotime($project['booking_date'])) : '-' ?></div>
</div>
<div class="row mt-2">
    <div class="col-lg-3 col-md-4 label text-muted">Signoff Date</div>
    <div class="col-lg-9 col-md-8"><?= $project['signoff_date'] ? date('d M Y', strtotime($project['signoff_date'])) : '-' ?></div>
</div>