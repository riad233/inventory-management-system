<div class="breadcrumb-nav">
    <a href="?url=employee/index">Employees</a> &gt; <span>Add Employee</span>
</div>

<?php require_once __DIR__ . '/../../../config/dropdown_helper.php'; ?>

<div class="page-title">
    <i class="fas fa-user-plus"></i> Add New Employee
</div>

<div class="card shadow card-max-600">
    <div class="card-body">
        <?php if(isset($data['errors']) && !empty($data['errors'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix the following errors:</strong>
                <?php foreach($data['errors'] as $field => $message): ?>
                    <div class="mt-2">
                        • <strong><?php echo e($field); ?>:</strong> 
                        <?php echo e($message); ?>
                    </div>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <form method="post" action="?url=employee/add">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Employee Category</label>
                <select name="designation" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="Administration">Administration</option>
                    <option value="Faculty">Faculty</option>
                    <option value="Staff">Staff</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="dept_id" class="form-control" required>
                    <option value="">Select Department</option>
                    <?php echo DropdownHelper::renderOptions('departments'); ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="text-end mt-4">
                <a href="?url=employee/index" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Employee</button>
            </div>
        </form>
    </div>
</div>
