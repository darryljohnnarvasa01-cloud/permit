<?php
/**
 * Permit Type Model
 */

require_once __DIR__ . '/../core/model.php';

class PermitType extends Model {
    protected $table = 'permit_types';
    
    public function getActive() {
        return $this->where('status', 'active');
    }
    
    public function toggleStatus($id) {
        $permitType = $this->find($id);
        if ($permitType) {
            $newStatus = $permitType['status'] === 'active' ? 'inactive' : 'active';
            return $this->update($id, ['status' => $newStatus]);
        }
        return false;
    }
    
    public function getRequirementsList($id) {
        $permitType = $this->find($id);
        if ($permitType && !empty($permitType['requirements'])) {
            return explode(',', $permitType['requirements']);
        }
        return [];
    }
}
