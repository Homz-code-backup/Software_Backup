<?php
class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }
 
    /* ====================== 
       LOGIN
    ======================= */

    public function findActiveByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM users 
            WHERE email = ? AND status = 'Active'
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    /* ======================
       FORGOT PASSWORD
    ======================= */

    public function saveResetToken(int $userId, string $token): void
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET token = ?
            WHERE id = ?
        ");
        $stmt->execute([$token, $userId]);
    }

    public function findByResetToken(string $token): ?User
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM users 
            WHERE token = ?
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function updatePassword(int $userId, string $hash): void
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET password_hash = ?, token = NULL
            WHERE id = ?
        ");
        $stmt->execute([$hash, $userId]);
    }
    public function createUser(array $data): void
    {
        $stmt = $this->db->prepare("INSERT INTO users (employee_id, email, password_hash, role, token, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['employee_id'],
            $data['email'],
            $data['password_hash'],
            $data['role'],
            $data['token'],
            $data['status']
        ]);
    }

    private function mapToModel(array $row): User
    {
        return new User($row);
    }
}
