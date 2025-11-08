<?php
/**
 * User Model
 */

require_once __DIR__ . '/../core/model.php';

class User extends Model {
    protected $table = 'users';
    
    public function authenticate($email, $password) {
        $user = $this->first('email', $email);
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'inactive') {
                return false;
            }
            return $user;
        }
        
        return false;
    }
    
    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->create($data);
    }
    
    public function updatePassword($userId, $newPassword) {
        return $this->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }
    
    public function getUsersByRole($role) {
        return $this->where('role', $role);
    }
    
    public function getActiveUsers() {
        return $this->where('status', 'active');
    }
    
    public function toggleStatus($userId) {
        $user = $this->find($userId);
        if ($user) {
            $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
            return $this->update($userId, ['status' => $newStatus]);
        }
        return false;
    }
    
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
