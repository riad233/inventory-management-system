<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . '/config/validator.php';

/**
 * FormValidator — per-form validation wrappers.
 * Delegates to the Validator class; avoids duplicating
 * the same Validator::reset() / field calls in every controller.
 */
class FormValidator {

    public static function validateAsset(array $data): bool {
        Validator::reset();

        Validator::required('name', $data['name'] ?? '', 'Asset Name');
        Validator::maxLength('name', $data['name'] ?? '', 255, 'Asset Name');

        Validator::required('category', $data['category'] ?? '', 'Category');
        Validator::maxLength('category', $data['category'] ?? '', 100, 'Category');

        Validator::required('brand', $data['brand'] ?? '', 'Brand');
        Validator::maxLength('brand', $data['brand'] ?? '', 100, 'Brand');

        Validator::maxLength('model', $data['model'] ?? '', 100, 'Model');
        Validator::maxLength('serial', $data['serial'] ?? '', 100, 'Serial Number');

        if (!empty($data['purchase_date'])) {
            Validator::date('purchase_date', $data['purchase_date'], 'Purchase Date');
        }

        if (!empty($data['warranty'])) {
            Validator::date('warranty', $data['warranty'], 'Warranty Expiry');
        }

        $validStatuses = ['Available', 'Assigned', 'Under Repair', 'Damaged', 'Disposed'];
        Validator::in('status', $data['status'] ?? 'Available', $validStatuses, 'Status');

        if (!empty($data['vendor_id'])) {
            Validator::integer('vendor_id', $data['vendor_id'], 'Vendor');
        }

        return Validator::passes();
    }

    public static function validateEmployee(array $data): bool {
        Validator::reset();

        Validator::required('name', $data['name'] ?? '', 'Name');
        Validator::maxLength('name', $data['name'] ?? '', 100, 'Name');

        Validator::required('designation', $data['designation'] ?? '', 'Designation');
        Validator::maxLength('designation', $data['designation'] ?? '', 100, 'Designation');

        Validator::required('dept_id', $data['dept_id'] ?? '', 'Department');
        Validator::integer('dept_id', $data['dept_id'] ?? '', 'Department');

        if (!empty($data['contact'])) {
            Validator::phone('contact', $data['contact'], 'Contact Number');
        }

        if (!empty($data['email'])) {
            Validator::email('email', $data['email'], 'Email');
        }

        return Validator::passes();
    }

    public static function validateVendor(array $data): bool {
        Validator::reset();

        Validator::required('vendor_name', $data['vendor_name'] ?? '', 'Vendor Name');
        Validator::maxLength('vendor_name', $data['vendor_name'] ?? '', 100, 'Vendor Name');

        if (!empty($data['email'])) {
            Validator::email('email', $data['email'], 'Email');
        }

        if (!empty($data['contact'])) {
            Validator::phone('contact', $data['contact'], 'Contact Number');
        }

        Validator::maxLength('address', $data['address'] ?? '', 200, 'Address');

        return Validator::passes();
    }

    public static function validateAssignment(array $data): bool {
        Validator::reset();

        Validator::required('asset_id', $data['asset_id'] ?? '', 'Asset');
        Validator::integer('asset_id', $data['asset_id'] ?? '', 'Asset');

        Validator::required('user_id', $data['user_id'] ?? '', 'Employee');
        Validator::integer('user_id', $data['user_id'] ?? '', 'Employee');

        Validator::required('dept_id', $data['dept_id'] ?? '', 'Department');
        Validator::integer('dept_id', $data['dept_id'] ?? '', 'Department');

        if (!empty($data['exp_return_date'])) {
            Validator::date('exp_return_date', $data['exp_return_date'], 'Expected Return Date');
        }

        return Validator::passes();
    }

    public static function validateMaintenance(array $data): bool {
        Validator::reset();

        Validator::required('asset_id', $data['asset_id'] ?? '', 'Asset');
        Validator::integer('asset_id', $data['asset_id'] ?? '', 'Asset');

        Validator::required('reported_date', $data['reported_date'] ?? '', 'Reported Date');
        Validator::date('reported_date', $data['reported_date'] ?? '', 'Reported Date');

        if (!empty($data['repair_start'])) {
            Validator::date('repair_start', $data['repair_start'], 'Repair Start Date');
        }

        if (!empty($data['repair_end'])) {
            Validator::date('repair_end', $data['repair_end'], 'Repair End Date');
        }

        if (!empty($data['cost'])) {
            Validator::numeric('cost', $data['cost'], 'Cost');
        }

        return Validator::passes();
    }

    public static function validateEquipmentRequest(array $data): bool {
        Validator::reset();

        Validator::required('user_id', $data['user_id'] ?? '', 'Employee');
        Validator::integer('user_id', $data['user_id'] ?? '', 'Employee ID');

        Validator::required('equipment_type', $data['equipment_type'] ?? '', 'Equipment Type');
        Validator::maxLength('equipment_type', $data['equipment_type'] ?? '', 255, 'Equipment Type');

        Validator::required('description', $data['description'] ?? '', 'Description');
        Validator::maxLength('description', $data['description'] ?? '', 1000, 'Description');

        return Validator::passes();
    }
}
