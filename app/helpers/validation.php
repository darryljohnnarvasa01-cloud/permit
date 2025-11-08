<?php
/**
 * Validation Helper Functions
 */

class Validator {
    private $errors = [];
    private $data = [];
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function validate($rules) {
        foreach ($rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            
            foreach ($rules as $rule) {
                $this->applyRule($field, $rule);
            }
        }
        
        return empty($this->errors);
    }
    
    private function applyRule($field, $rule) {
        $value = $this->data[$field] ?? null;
        
        if (strpos($rule, ':') !== false) {
            list($rule, $param) = explode(':', $rule, 2);
        }
        
        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, ucfirst($field) . ' is required');
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'Invalid email format');
                }
                break;
                
            case 'min':
                if (!empty($value) && strlen($value) < $param) {
                    $this->addError($field, ucfirst($field) . " must be at least $param characters");
                }
                break;
                
            case 'max':
                if (!empty($value) && strlen($value) > $param) {
                    $this->addError($field, ucfirst($field) . " must not exceed $param characters");
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, ucfirst($field) . ' must be a number');
                }
                break;
                
            case 'alpha':
                if (!empty($value) && !ctype_alpha(str_replace(' ', '', $value))) {
                    $this->addError($field, ucfirst($field) . ' must contain only letters');
                }
                break;
                
            case 'alphanumeric':
                if (!empty($value) && !ctype_alnum(str_replace([' ', '-', '_'], '', $value))) {
                    $this->addError($field, ucfirst($field) . ' must contain only letters and numbers');
                }
                break;
                
            case 'unique':
                list($table, $column) = explode(',', $param);
                if (!empty($value) && $this->exists($table, $column, $value)) {
                    $this->addError($field, ucfirst($field) . ' already exists');
                }
                break;
        }
    }
    
    private function exists($table, $column, $value) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
        $stmt->execute([$value]);
        return $stmt->fetchColumn() > 0;
    }
    
    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getFirstError($field = null) {
        if ($field) {
            return $this->errors[$field][0] ?? null;
        }
        
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0];
        }
        
        return null;
    }
}

function validate($data, $rules) {
    $validator = new Validator($data);
    $validator->validate($rules);
    return $validator;
}
