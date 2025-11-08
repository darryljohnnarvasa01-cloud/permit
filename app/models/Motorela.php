<?php
/**
 * Motorela Permit Model
 */

require_once __DIR__ . '/../core/model.php';

class Motorela extends Model {
    protected $table = 'motorela_permits';
    
    public function getByUser($userId) {
        return $this->where('user_id', $userId);
    }
    
    public function getByStatus($status) {
        return $this->where('status', $status);
    }
    
    public function getPending() {
        return $this->getByStatus('pending');
    }
    
    public function getApproved() {
        return $this->getByStatus('approved');
    }
    
    public function getAllWithDetails() {
        $sql = "SELECT 
            mp.*,
            u.fullname as applicant_name,
            pt.name as permit_type_name,
            vt.name as vehicle_type_name,
            rt.name as renewal_type_name,
            fz.name as fare_zone_name,
            cg.name as color_group_name
        FROM motorela_permits mp
        LEFT JOIN users u ON mp.user_id = u.id
        LEFT JOIN permit_types pt ON mp.permit_type_id = pt.id
        LEFT JOIN categories vt ON mp.vehicle_type_id = vt.id
        LEFT JOIN categories rt ON mp.renewal_type_id = rt.id
        LEFT JOIN categories fz ON mp.fare_zone_id = fz.id
        LEFT JOIN categories cg ON mp.color_group_id = cg.id
        ORDER BY mp.created_at DESC";
        
        return $this->query($sql);
    }
    
    public function getWithDetails($id) {
        $sql = "SELECT 
            mp.*,
            u.fullname as applicant_name,
            u.email as applicant_email,
            pt.name as permit_type_name,
            pt.fee as permit_fee,
            vt.name as vehicle_type_name,
            rt.name as renewal_type_name,
            fz.name as fare_zone_name,
            cg.name as color_group_name,
            approver.fullname as approved_by_name
        FROM motorela_permits mp
        LEFT JOIN users u ON mp.user_id = u.id
        LEFT JOIN permit_types pt ON mp.permit_type_id = pt.id
        LEFT JOIN categories vt ON mp.vehicle_type_id = vt.id
        LEFT JOIN categories rt ON mp.renewal_type_id = rt.id
        LEFT JOIN categories fz ON mp.fare_zone_id = fz.id
        LEFT JOIN categories cg ON mp.color_group_id = cg.id
        LEFT JOIN users approver ON mp.approved_by = approver.id
        WHERE mp.id = ?";
        
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    public function approve($id, $approvedBy) {
        return $this->update($id, [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $approvedBy
        ]);
    }
    
    public function reject($id, $reason) {
        return $this->update($id, [
            'status' => 'rejected',
            'rejected_reason' => $reason
        ]);
    }
    
    public function updateQRCode($id, $qrPath) {
        return $this->update($id, ['qr_code' => $qrPath]);
    }
    
    public function updatePaymentStatus($id, $status, $amount = null) {
        $data = ['payment_status' => $status];
        if ($amount !== null) {
            $data['payment_amount'] = $amount;
        }
        return $this->update($id, $data);
    }
    
    public function getExpiringSoon($days = 30) {
        $date = date('Y-m-d', strtotime("+$days days"));
        $sql = "SELECT * FROM motorela_permits 
                WHERE status = 'approved' 
                AND expiration_date <= ? 
                AND expiration_date >= CURDATE()
                ORDER BY expiration_date";
        return $this->query($sql, [$date]);
    }
    
    public function getStatistics() {
        $sql = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired,
            SUM(CASE WHEN payment_status = 'paid' THEN payment_amount ELSE 0 END) as total_revenue
        FROM motorela_permits";
        
        $result = $this->query($sql);
        return $result[0] ?? [];
    }
    
    public function plateNumberExists($plateNumber, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM motorela_permits WHERE plate_number = ?";
        $params = [$plateNumber];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
