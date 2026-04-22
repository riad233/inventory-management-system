<?php
/**
 * Form Error Component
 * Displays validation errors in a reusable Bootstrap 5 format
 * 
 * Include this in your forms with:
 * <?php require __DIR__ . '/../components/form-error.php'; ?>
 */
?>

<?php if(isset($data['errors']) && !empty($data['errors'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</strong>
        <?php foreach($data['errors'] as $field => $message): ?>
            <div class="mt-2">
                <i class="fas fa-times-circle text-danger"></i> 
                <strong><?php echo e($field); ?>:</strong> 
                <?php echo e($message); ?>
            </div>
        <?php endforeach; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
