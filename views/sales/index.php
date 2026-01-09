    <div class="d-flex justify-content-between align-items-center">
        <h1>Projects</h1>
        <button class="btn btn-dark" type="button" onclick="addProject()">
            <i class="bi bi-plus-lg"></i> Add Project
        </button>
    </div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_PATH . '/dashboard' ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Projects</li>
        </ol>
    </nav>
    </div>

    <ul class="nav nav-tabs mb-3" id="salesTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="registered-tab" data-bs-toggle="tab" type="button" role="tab" onclick="switchTab('registered')">Active</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="booked-tab" data-bs-toggle="tab" type="button" role="tab" onclick="switchTab('booked')">Booked</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="hold-tab" data-bs-toggle="tab" type="button" role="tab" onclick="switchTab('hold')">Hold</button>
        </li>
    </ul>

    <?php
    $tableConfig = [
        'id' => 'salesTable',
        'title' => 'Estimates',

        'columns' => [
            'sno' => ['label' => 'S.No', 'visible' => true, 'locked' => true],
            'project_reference' => ['label' => 'Project Reference', 'visible' => true, 'locked' => true],
            'name' => ['label' => 'Customer Name', 'visible' => true, 'locked' => true],
            'email' => ['label' => 'Email', 'visible' => true],
            'project_name' => ['label' => 'Project', 'visible' => true],
            'contact_no' => ['label' => 'Contact', 'visible' => true],
            'branch_name' => ['label' => 'Branch', 'visible' => true],
            'city_name' => ['label' => 'City', 'visible' => true],
            'status' => ['label' => 'Status', 'visible' => true]
        ],

        'search' => [
            'placeholder' => 'Search name / email / project name / project reference no',
            'fields' => ['name', 'email', 'project_name', 'project_reference']
        ],

        'filters' => [
            [
                'key' => 'city_id',
                'label' => 'City',
                'options' => array_map(fn($c) => [
                    'value' => $c['id'],
                    'label' => $c['name']
                ], $data['cities'])
            ],
            [
                'key' => 'branch_id',
                'label' => 'Branch',
                'options' => array_map(fn($b) => [
                    'value' => $b['id'],
                    'label' => $b['branch_name'],
                    'dataset' => ['city-id' => $b['city_id']]
                ], $data['branches'])
            ]
        ],

        'initialFilters' => [
            'tab' => 'registered'
        ],

        'endpoint' => BASE_PATH . '/api/estimates'
    ];

    include __DIR__ . '/../components/advanced-table.php';

    ?>
    <script>
        function switchTab(tab) {
            erpTable.filter('salesTable', 'tab', tab);

            // Update UI
            document.querySelectorAll('#salesTabs .nav-link').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tab + '-tab').classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const cityFilter = document.getElementById('filter_city_id');
            const branchFilter = document.getElementById('filter_branch_id');

            if (cityFilter && branchFilter) {
                const allBranches = Array.from(branchFilter.options);

                cityFilter.addEventListener('change', function() {
                    const selectedCityId = this.value;
                    branchFilter.innerHTML = '';
                    branchFilter.appendChild(allBranches[0]);

                    allBranches.slice(1).forEach(option => {
                        const branchCityId = option.dataset.cityId;
                        if (!selectedCityId || branchCityId == selectedCityId) {
                            branchFilter.appendChild(option);
                        }
                    });

                    branchFilter.value = '';
                    erpTable.filter('salesTable', 'branch_id', '');
                });
            }
        });
    </script>

    <!-- Project Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="projectOffcanvas" aria-labelledby="projectOffcanvasLabel" style="width: 500px;">
        <div class="offcanvas-header border-bottom">
            <h5 id="projectOffcanvasLabel">Project Details</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="projectForm">
                <input type="hidden" name="id" id="proj_id">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Client Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="proj_name" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="proj_email" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact No <span class="text-danger">*</span></label>
                        <input type="text" name="contact_no" id="proj_contact" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alternate No</label>
                        <input type="text" name="alternate_no" id="proj_alternate" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Project Name</label>
                        <input type="text" name="project_name" id="proj_project_name" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Project Address</label>
                        <textarea name="project_address" id="proj_address" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <select name="city" id="proj_city" class="form-select">
                            <option value="">Select City</option>
                            <?php foreach ($data['cities'] as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" id="proj_branch" class="form-select">
                            <option value="">Select Branch</option>
                            <?php foreach ($data['branches'] as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= $b['branch_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PAN Number</label>
                        <input type="text" name="pan_number" id="proj_pan" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">GST Percentage</label>
                        <input type="number" step="0.01" name="gst_percentage" id="proj_gst" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">House Type</label>
                        <select name="house_type" id="proj_house_type" class="form-select">
                            <option value="flat">Flat</option>
                            <option value="villa">Villa</option>
                            <option value="independent house">Independent House</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Flat No</label>
                        <input type="text" name="flat_no" id="proj_flat_no" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">BHK Type</label>
                        <input type="text" name="bhk_type" id="proj_bhk" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Floors</label>
                        <input type="number" name="floors" id="proj_floors" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Project Type</label>
                        <select name="project_type" id="proj_project_type" class="form-select">
                            <option value="Residential">Residential</option>
                            <option value="Commercial">Commercial</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Booking Date</label>
                        <input type="date" name="booking_date" id="proj_booking_date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Signoff Date</label>
                        <input type="date" name="signoff_date" id="proj_signoff_date" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Relationship Manager</label>
                        <select name="relationship_manager" id="proj_rm" class="form-select">
                            <option value="">Select RM</option>
                            <?php foreach ($data['employees'] as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>"><?= $emp['full_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designer</label>
                        <select name="designer" id="proj_designer" class="form-select">
                            <option value="">Select Designer</option>
                            <?php foreach ($data['employees'] as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>"><?= $emp['full_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">QC Member</label>
                        <select name="qc_team_member_id" id="proj_qc" class="form-select">
                            <option value="">Select QC</option>
                            <?php foreach ($data['employees'] as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>"><?= $emp['full_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mt-4 border-top pt-3 d-grid">
                        <button type="submit" class="btn btn-dark">Save Project</button>
                    </div>
            </form>
        </div>
    </div>

    <script>
        const projectForm = document.getElementById('projectForm');

        function addProject() {
            projectForm.reset();
            document.getElementById('proj_id').value = '';
            document.getElementById('projectOffcanvasLabel').innerText = 'Add New Project';

            const offcanvasElement = document.getElementById('projectOffcanvas');
            const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);
            bsOffcanvas.show();
        }

        function editProject(token) {
            fetch('<?= BASE_PATH ?>/api/project/get?token=' + token)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('proj_id').value = data.id;
                    document.getElementById('proj_name').value = data.name;
                    document.getElementById('proj_email').value = data.email;
                    document.getElementById('proj_contact').value = data.contact_no;
                    document.getElementById('proj_alternate').value = data.alternate_no;
                    document.getElementById('proj_project_name').value = data.project_name;
                    document.getElementById('proj_address').value = data.project_address;
                    document.getElementById('proj_city').value = data.city;
                    document.getElementById('proj_branch').value = data.branch_id;
                    document.getElementById('proj_house_type').value = data.house_type;
                    document.getElementById('proj_bhk').value = data.bhk_type;

                    // New fields
                    document.getElementById('proj_pan').value = data.pan_number || '';
                    document.getElementById('proj_flat_no').value = data.flat_no || '';
                    document.getElementById('proj_floors').value = data.floors || '';
                    document.getElementById('proj_gst').value = data.gst_percentage || '';
                    document.getElementById('proj_project_type').value = data.project_type || 'Residential';
                    document.getElementById('proj_booking_date').value = data.booking_date || '';
                    document.getElementById('proj_signoff_date').value = data.signoff_date || '';
                    document.getElementById('proj_rm').value = data.relationship_manager || '';
                    document.getElementById('proj_designer').value = data.designer || '';
                    document.getElementById('proj_qc').value = data.qc_team_member_id || '';

                    document.getElementById('projectOffcanvasLabel').innerText = 'Edit Project';

                    const offcanvasElement = document.getElementById('projectOffcanvas');
                    const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);
                    bsOffcanvas.show();
                });
        }

        projectForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('<?= BASE_PATH ?>/api/project/save', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const offcanvasElement = document.getElementById('projectOffcanvas');
                        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                        if (bsOffcanvas) bsOffcanvas.hide();

                        // Redirect to view page
                        if (res.token) {
                            window.location.href = '<?= BASE_PATH ?>/projects/view/' + res.token;
                        } else {
                            erpTable.load('salesTable');
                        }
                    } else {
                        alert('Error saving project');
                    }
                });
        }
    </script>