<div class="pagetitle">
    <h1>Employee Master</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_PATH . '/dashboard' ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Employee Master</li>
        </ol>
    </nav>
</div>

<ul class="nav nav-tabs mb-3" id="employeeTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="active-tab" data-bs-toggle="tab" type="button" role="tab" onclick="switchTab('Active')">Active</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="inactive-tab" data-bs-toggle="tab" type="button" role="tab" onclick="switchTab('Inactive')">Inactive</button>
    </li>
</ul>

<?php
$tableConfig = [
    'id' => 'employeeTable',
    'title' => 'Employees',

    'columns' => [
        'sno' => ['label' => 'S.No', 'visible' => true, 'locked' => true],
        'id' => ['label' => 'ID', 'visible' => true, 'locked' => true],
        'employee_id' => 'Emp ID',
        'full_name' => ['label' => 'Name', 'visible' => true, 'locked' => true],
        'official_email' => ['label' => 'Email', 'visible' => true],
        'job_position' => ['label' => 'Position', 'visible' => false], // Hidden by default
        'department_name' => ['label' => 'Department', 'visible' => true],
        'branch_name' => ['label' => 'Branch', 'visible' => true],
        'city_name' => ['label' => 'City', 'visible' => true]
    ],

    'search' => [
        'placeholder' => 'Search name / email',
        'fields' => ['full_name', 'official_email']
    ],

    'filters' => [
        [
            'key' => 'department_id',
            'label' => 'Department',
            'options' => array_map(fn($d) => [
                'value' => $d['id'],
                'label' => $d['department_name']
            ], $data['departments'])
        ],
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
        ],
        [
            'key' => 'job_position',
            'label' => 'Position',
            'options' => array_map(fn($p) => [
                'value' => $p['job_position'],
                'label' => $p['job_position']
            ], $data['job_positions'])
        ]
    ],

    'initialFilters' => [
        'status' => ['Active', 'Registered']
    ],

    'endpoint' => BASE_PATH . '/api/employees'
];

include __DIR__ . '/../components/advanced-table.php';
?>

<script>
    function switchTab(status) {
        const filterStatus = status === 'Active' ? ['Active', 'Registered'] : ['Inactive'];
        erpTable.filter('employeeTable', 'status', filterStatus);

        // Update UI
        document.querySelectorAll('#employeeTabs .nav-link').forEach(btn => btn.classList.remove('active'));
        if (status === 'Active') document.getElementById('active-tab').classList.add('active');
        else document.getElementById('inactive-tab').classList.add('active');
    }

    async function statusUpdate(id, status) {
        if (!confirm(`Are you sure you want to ${status === 'Inactive' ? 'deactivate' : 'reactivate'} this employee?`)) return;

        try {
            const response = await fetch('<?= BASE_PATH ?>/employees/statusupdate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id,
                    status
                })
            });
            const res = await response.json();
            if (res.success) {
                erpTable.load('employeeTable');
            } else {
                alert(res.message);
            }
        } catch (e) {
            alert('Error updating status');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const cityFilter = document.getElementById('filter_city_id');
        const branchFilter = document.getElementById('filter_branch_id');

        if (cityFilter && branchFilter) {
            // Keep original options to restore them
            const allBranches = Array.from(branchFilter.options);

            cityFilter.addEventListener('change', function() {
                const selectedCityId = this.value;

                // Clear current options
                branchFilter.innerHTML = '';

                // Always add "All Branch" option (first one)
                branchFilter.appendChild(allBranches[0]);

                allBranches.slice(1).forEach(option => {
                    const branchCityId = option.dataset.cityId;
                    if (!selectedCityId || branchCityId == selectedCityId) {
                        branchFilter.appendChild(option);
                    }
                });

                // Trigger change event to update table if needed (though erpTable usually handles its own filter calls)
                // But dependent dropdowns might need to reset value if current value is invalid.
                branchFilter.value = '';
                // We should also trigger the table filter update if we reset the branch
                erpTable.filter('employeeTable', 'branch_id', '');
            });
        }
    });
</script>