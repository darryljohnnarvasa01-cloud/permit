<?php
/**
 * Activity Log Model
 */

require_once __DIR__ . '/../core/model.php';

class Log extends Model {
    protected $table = 'logs';
    
    public function add($userId, $activity, $description = null) {
        // Check if user exists, if not, use NULL
        $userExists = $this->userExists($userId);
        
        $data = [
            'user_id' => $userExists ? $userId : null,
            'activity' => $activity,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];
        
        return $this->create($data);
    }
    
    private function userExists($userId) {
        if (empty($userId)) {
            return false;
        }
        
        $sql = "SELECT COUNT(*) FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function getRecent($limit = 50) {
        $sql = "SELECT l.*, u.fullname, u.email, u.role 
                FROM logs l
                LEFT JOIN users u ON l.user_id = u.id
                ORDER BY l.created_at DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getByUser($userId, $limit = 50) {
        $sql = "SELECT * FROM logs 
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    public function getByDateRange($startDate, $endDate) {
        $sql = "SELECT l.*, u.fullname, u.email, u.role 
                FROM logs l
                LEFT JOIN users u ON l.user_id = u.id
                WHERE DATE(l.created_at) BETWEEN ? AND ?
                ORDER BY l.created_at DESC";
        
        return $this->query($sql, [$startDate, $endDate]);
    }
    
    public function deleteOlderThan($days) {
        $date = date('Y-m-d', strtotime("-$days days"));
        $sql = "DELETE FROM logs WHERE DATE(created_at) < ?";
        return $this->execute($sql, [$date]);
    }
}
