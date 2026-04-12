<div class="breadcrumb-nav">
    <a href="?url=employee/index">Employees</a> &gt; <span>Edit Employee</span>
</div>

<div class="page-title">
    <i class="fas fa-user-edit"></i> Edit Employee
</div>

<div class="card shadow" style="max-width: 600px;">
    <div class="card-body">
        <?php if(!isset($data['employee']) || empty($data['employee'])): ?>
            <div class="alert alert-danger">Employee not found!</div>
            <a href="?url=employee/index" class="btn btn-secondary">Go Back</a>
        <?php else: $emp = $data['employee']; ?>
        <form method="post" action="?url=employee/edit/<?php echo e($emp['User_ID']); ?>">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($emp['Name']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Employee Category</label>
                <select name="designation" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="Administration" <?php echo ($emp['Designation'] == 'Administration') ? 'selected' : ''; ?>>Administration</option>
                    <option value="Faculty" <?php echo ($emp['Designation'] == 'Faculty') ? 'selected' : ''; ?>>Faculty</option>
                    <option value="Staff" <?php echo ($emp['Designation'] == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                    <option value="Other" <?php echo ($emp['Designation'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="dept_id" class="form-control" required>
                    <option value="">Select Department</option>
                    <?php if(isset($data['departments']) && is_array($data['departments'])): ?>
                        <?php foreach($data['departments'] as $dept): ?>
                            <option value="<?php echo e($dept['Department_ID']); ?>" <?php echo ($dept['Department_ID'] == $emp['Department_ID']) ? 'selected' : ''; ?>>
                                <?php echo e($dept['Department_Name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact" class="form-control" value="<?php echo e($emp['Contact_Number']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="<?php echo e($emp['Email']); ?>" required>
            </div>
            
            <div class="text-end mt-4">
                <a href="?url=employee/index" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Employee</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>
