<?php
require_once 'config/dropdown_helper.php';

echo "Testing DropdownHelper:\n";

// Test load
$data = DropdownHelper::load();
echo "Loaded data keys: " . implode(", ", array_keys($data)) . "\n";

// Test get vendor_types
$types = DropdownHelper::get('vendor_types');
echo "Vendor Types count: " . count($types) . "\n";
if (!empty($types)) {
    echo "First type: " . json_encode($types[0]) . "\n";
}

// Test get vendor_status
$status = DropdownHelper::get('vendor_status');
echo "Vendor Status count: " . count($status) . "\n";
if (!empty($status)) {
    echo "First status: " . json_encode($status[0]) . "\n";
}

// Test renderOptions
echo "\nRendering vendor_types:\n";
echo DropdownHelper::renderOptions('vendor_types');

echo "\nRendering vendor_status:\n";
echo DropdownHelper::renderOptions('vendor_status');
?>
