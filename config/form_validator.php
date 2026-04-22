<?php
/**
 * Form Validator Helper - Centralizes validation logic
 * Provides reusable validation patterns for controllers
 */

class FormValidator {
    
    /**
     * Validate asset form data
     */
    public static function validateAsset($data) {
        Validator::reset();
        Validator::required('name', $data['name'] ?? '', 'Asset Name');
        Validator::required('category', $data['category'] ?? '', 'Category');
        Validator::required('brand', $data['brand'] ?? '', 'Brand');
        Validator::required('model', $data['model'] ?? '', 'Model');
        Validator::required('serial', $data['serial'] ?? '', 'Serial Number');
        Validator::required('purchase_date', $data['purchase_date'] ?? '', 'Purchase Date');
        Validator::date('purchase_date', $data['purchase_date'] ?? '', 'Purchase Date');
        return Validator::passes();
    }
    
    /**
     * Validate employee form data
     */
    public static function validateEmployee($data) {
        Validator::reset();
        Validator::required('name', $data['name'] ?? '', 'Name');
        Validator::required('designation', $data['designation'] ?? '', 'Designation');
        Validator::required('dept_id', $data['dept_id'] ?? '', 'Department');
        Validator::required('contact', $data['contact'] ?? '', 'Contact Number');
        Validator::required('email', $data['email'] ?? '', 'Email');
        Validator::email('email', $data['email'] ?? '', 'Email');
        return Validator::passes();
    }
    
    /**
     * Validate vendor form data
     */
    public static function validateVendor($data) {
        Validator::reset();
        Validator::required('vendor_name', $data['vendor_name'] ?? '', 'Vendor Name');
        Validator::required('contact_person', $data['contact_person'] ?? '', 'Contact Person');
        Validator::required('contact_number', $data['contact_number'] ?? '', 'Contact Number');
        Validator::required('email', $data['email'] ?? '', 'Email');
        Validator::email('email', $data['email'] ?? '', 'Email');
        Validator::required('address', $data['address'] ?? '', 'Address');
        return Validator::passes();
    }
    
    /**
     * Validate assignment form data
     */
    public static function validateAssignment($data) {
        Validator::reset();
        Validator::required('asset_id', $data['asset_id'] ?? '', 'Asset');
        Validator::required('user_id', $data['user_id'] ?? '', 'Employee');
        Validator::required('dept_id', $data['dept_id'] ?? '', 'Department');
        Validator::required('exp_return_date', $data['exp_return_date'] ?? '', 'Return Date');
        Validator::date('exp_return_date', $data['exp_return_date'] ?? '', 'Return Date');
        return Validator::passes();
    }
    
    /**
     * Validate maintenance form data
     */
    public static function validateMaintenance($data) {
        Validator::reset();
        Validator::required('asset_id', $data['asset_id'] ?? '', 'Asset');
        Validator::required('maintenance_status', $data['maintenance_status'] ?? '', 'Status');
        Validator::required('cost', $data['cost'] ?? '', 'Cost');
        Validator::numeric('cost', $data['cost'] ?? '', 'Cost');
        if (($data['cost'] ?? 0) > 0) {
            Validator::positive('cost', $data['cost'], 'Cost');
        }
        return Validator::passes();
    }
    
    /**
     * Validate maintenance status update
     */
    public static function validateMaintenanceUpdate($data) {
        Validator::reset();
        Validator::required('maintenance_id', $data['maintenance_id'] ?? '', 'Maintenance ID');
        Validator::required('status', $data['status'] ?? '', 'Status');
        $validStatuses = ['Pending', 'In Progress', 'Completed'];
        if (!in_array($data['status'] ?? '', $validStatuses)) {
            Validator::addError('status', 'Invalid status selected');
        }
        if (isset($data['end_date']) && !empty($data['end_date'])) {
            Validator::date('end_date', $data['end_date'], 'Completion Date');
        }
        return Validator::passes();
    }
    
    /**
     * Validate request equipment form data
     */
    public static function validateEquipmentRequest($data) {
        Validator::reset();
        Validator::required('user_id', $data['user_id'] ?? '', 'Employee');
        Validator::required('equipment_type', $data['equipment_type'] ?? '', 'Equipment Type');
        return Validator::passes();
    }
}
?>
