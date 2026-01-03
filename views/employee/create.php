
<div class="pagetitle mb-4">
    <h1>Register Employee</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="../employees">Employees</a></li>
            <li class="breadcrumb-item active">Register Employee</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Register Employee</h5>

            <form method="POST" action="<?= BASE_PATH ?>/employees/store" novalidate>

                <!-- EMPLOYEE ID + NAME -->
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Employee ID</label>
                        <input type="text"
                               name="employee_id"
                               class="form-control"
                               required
                               pattern="[A-Z0-9]+"
                               minlength="3"
                               maxlength="20"
                               placeholder="EMP001"
                               title="Only uppercase letters and numbers, no spaces">
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label">Full Name</label>
                        <input type="text"
                               name="full_name"
                               class="form-control"
                               required
                               pattern="[A-Za-z ]{3,}"
                               placeholder="John Doe"
                               title="Only letters and spaces, minimum 3 characters">
                    </div>
                </div>

                <!-- EMAIL + PHONE -->
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Official Email</label>
                        <input type="email"
                               name="official_email"
                               class="form-control"
                               required
                               placeholder="name@company.in">
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label">Official Number</label>
                        <input type="text"
                               name="official_number"
                               class="form-control"
                               required
                               pattern="[6-9][0-9]{9}"
                               maxlength="10"
                               placeholder="10 digit mobile number"
                               title="Enter a valid 10-digit mobile number">
                    </div>
                </div>

                <!-- DOJ + POSITION + BRANCH -->
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label">Date of Joining</label>
                        <input type="date"
                               name="date_of_joining"
                               class="form-control"
                               required value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="col-sm-4">
                        <label class="form-label">Job Position</label>
                        <input type="text"
                               name="job_position"
                               class="form-control"
                               required
                               minlength="2"
                               placeholder="Sales Executive">
                    </div>

                    <div class="col-sm-4">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-select" required>
                            <option value="" disabled selected>-- Select Branch --</option>
                            <?php foreach ($branches as $b): ?>
                                <option value="<?= $b['id'] ?>">
                                    <?= htmlspecialchars($b['branch_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- DEPARTMENT + COMPANY -->
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select" required>
                            <option value="" disabled selected>-- Select Department --</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= htmlspecialchars($d['department_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label">Company</label>
                        <select name="company_id" class="form-select" required>
                            <!-- <option value="" disabled selected>-- Select Company --</option> -->
                            <?php foreach ($companies as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- SUBMIT -->
                <div class="text-center w-100">
                    <button class="btn btn-dark px-4 w-100">Submit</button>
                </div>

            </form>
        </div>
    </div>
</div>
