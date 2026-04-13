<div class="page-title">
    <i class="fas fa-users"></i> Employees
</div>

<nav class="navbar navbar-expand-lg content-action-bar p-0">
    <div class="container-fluid">
        <div class="collapse navbar-collapse justify-content-end gap-2">
            <div class="action-search">
                <input type="text" id="employeeSearchInput" data-search-table="employeeTable" placeholder="Search employees...">
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
                <th>Actions</th>
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
                    <td data-label="Actions">
                        <a href="?url=employee/edit/<?php echo e($emp['User_ID']); ?>" class="btn-action btn-action-edit" title="Edit"><i class="fas fa-edit"></i></a>
                        <form method="post" action="?url=employee/delete/<?php echo e($emp['User_ID']); ?>" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-action btn-action-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this employee?');"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

