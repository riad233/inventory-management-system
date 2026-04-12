<div class="breadcrumb-nav">
    <a href="?url=employee/index">Employees</a> &gt; <span>Add Employee</span>
</div>

<div class="page-title">
    <i class="fas fa-user-plus"></i> Add New Employee
</div>

<div class="card shadow" style="max-width: 600px;">
    <div class="card-body">
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
                    <?php if(isset($data['departments']) && is_array($data['departments'])): ?>
                        <?php foreach($data['departments'] as $dept): ?>
                            <option value="<?php echo e($dept['Department_ID']); ?>"><?php echo e($dept['Department_Name']); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
