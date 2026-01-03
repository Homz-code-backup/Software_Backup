<?php
class AuthService
{
    private UserRepository $repo;
    private EmployeeRepository $employeeRepo;

    public function __construct()
    {
        $this->repo = new UserRepository();
        $this->employeeRepo = new EmployeeRepository();
    }

    /* ======================
       LOGIN
    ======================= */

    public function login(string $email, string $password): bool
    { 
        $user = $this->repo->findActiveByEmail($email);

        if (!$user) return false;
        if (!password_verify($password, $user->password_hash)) return false;

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_employee_id'] = $user->employee_id;
        $employee = $this->employeeRepo->getEmployeeById($user->employee_id);
        if ($employee) {
            $_SESSION['employee_name'] = $employee->full_name ?? 'Unknown';
        } else {
            $_SESSION['employee_name'] = 'Unknown';
        }
        return true;
    }

    public function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_employee_id']);
    }

    /* ======================
       FORGOT PASSWORD
    ======================= */

    public function sendPasswordReset(string $email): void
    {
        $user = $this->repo->findActiveByEmail($email);
        if (!$user) return;

        $token = bin2hex(random_bytes(32));

        $this->repo->saveResetToken((int)$user->id, $token);

        $resetLink = BASE_PATH . "/reset-password?token=" . $token;

        // TEMP: replace with mail later
        error_log("RESET LINK: " . $resetLink);
    }

    public function resetPassword(string $token, string $password): bool
    {
        $user = $this->repo->findByResetToken($token);
        if (!$user) return false;

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $this->repo->updatePassword((int)$user->id, $hash);

        return true;
    }
}
