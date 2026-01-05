<div class="pagetitle">
    <h1>Clients</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_PATH . '/dashboard' ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Clients</li>
        </ol>
    </nav>
</div>

<ul class="nav nav-tabs mb-3" id="salesTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="draft-tab" data-bs-toggle="tab" type="button" role="tab" onclick="switchTab('draft')">Draft</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="registered-tab" data-bs-toggle="tab" type="button" role="tab" onclick="switchTab('registered')">Active</button>
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
        'fields' => ['name', 'email', 'project_name','project_reference']
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
        'active_status' => ['draft']
    ],

    'endpoint' => BASE_PATH . '/api/estimates'
];

include __DIR__ . '/../components/advanced-table.php';
?>

<script>
    function switchTab(status) {
        erpTable.filter('salesTable', 'active_status', [status]);

        // Update UI
        document.querySelectorAll('#salesTabs .nav-link').forEach(btn => btn.classList.remove('active'));
        document.getElementById(status + '-tab').classList.add('active');
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