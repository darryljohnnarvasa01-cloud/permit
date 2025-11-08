<?php
/**
 * Category Model
 */

require_once __DIR__ . '/../core/model.php';

class Category extends Model {
    protected $table = 'categories';
    
    public function getByType($type) {
        return $this->where('type', $type);
    }
    
    public function getActiveByType($type) {
        $sql = "SELECT * FROM categories WHERE type = ? AND status = 'active' ORDER BY name";
        return $this->query($sql, [$type]);
    }
    
    public function getAllGroupedByType() {
        $categories = $this->all();
        $grouped = [];
        
        foreach ($categories as $category) {
            $type = $category['type'];
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $category;
        }
        
        return $grouped;
    }
    
    public function getVehicleTypes() {
        return $this->getActiveByType('vehicle_type');
    }
    
    public function getRenewalTypes() {
        return $this->getActiveByType('renewal_type');
    }
    
    public function getFareZones() {
        return $this->getActiveByType('fare_zone');
    }
    
    public function getColorGroups() {
        return $this->getActiveByType('color_group');
    }
    
    public function toggleStatus($id) {
        $category = $this->find($id);
        if ($category) {
            $newStatus = $category['status'] === 'active' ? 'inactive' : 'active';
            return $this->update($id, ['status' => $newStatus]);
        }
        return false;
    }
}
