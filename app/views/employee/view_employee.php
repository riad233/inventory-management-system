<div class="page-title">
    <i class="fas fa-users"></i> Employees
</div>

<nav class="navbar navbar-expand-lg p-0" style="margin-bottom: 1rem;">
    <div class="container-fluid">
        <div class="collapse navbar-collapse justify-content-end gap-2">
            <div style="margin-right: 1rem;">
                <input type="text" id="employeeSearchInput" data-search-table="employeeTable" placeholder="Search employees..." style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <ul class="navbar-nav">
                <li class="nav-item"><a href="?url=employee/add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New Employee</a></li>
            </ul>
        </div>
    </div>
</nav>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo e($_GET['msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="table-container">
    <table class="table table-striped table-hover" id="employeeTable">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Department</th>
                <th>Contact</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data['employees']) && is_array($data['employees'])): ?>
                <?php foreach($data['employees'] as $emp): ?>
                <tr>
                    <td data-label="ID"><?php echo e($emp['User_ID']); ?></td>
                    <td data-label="Name"><strong><?php echo e($emp['Name']); ?></strong></td>
                    <td data-label="Category"><?php echo e($emp['Designation']); ?></td>
                    <td data-label="Department"><?php echo e(isset($emp['Department_Name']) ? $emp['Department_Name'] : 'N/A'); ?></td>
                    <td data-label="Contact"><?php echo e($emp['Contact_Number']); ?></td>
                    <td data-label="Email"><?php echo e($emp['Email']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

