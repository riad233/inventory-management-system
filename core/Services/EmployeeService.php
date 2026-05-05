<?php

use core\Exceptions\{ValidationException, ConflictException, NotFoundException};

/**
 * EmployeeService - Business logic for employee management
 */
class EmployeeService extends BaseService
{
    public function __construct()
    {
        parent::__construct(RepositoryFactory::employees());
    }

    /**
     * Create a new employee.
     *
     * @param array $data Employee data
     * @return int Employee ID
     * @throws ValidationException
     */
    public function createEmployee(array $data): int
    {
        $this->validateRequired($data, ['first_name', 'last_name', 'email', 'department_id']);

        $this->validate($data, [
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'email' => 'required|email',
            'department_id' => 'required|integer',
        ]);

        // Check if email already exists
        if ($this->exists(['email' => $data['email']])) {
            throw new ConflictException('Employee', 'Email already registered');
        }

        $id = $this->repository->create($data);
        $this->logAction('Employee created', ['employee_id' => $id, 'email' => $data['email']]);

        // Invalidate cache
        $this->invalidateOnCreate();

        return $id;
    }

    /**
     * Update employee information.
     *
     * @param int $id Employee ID
     * @param array $data Updated data
     * @return bool Success
     * @throws NotFoundException
     */
    public function updateEmployee(int $id, array $data): bool
    {
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Employee', $id);
        }

        $this->validate($data, [
            'first_name' => 'min:2',
            'last_name' => 'min:2',
            'email' => 'email',
            'department_id' => 'integer',
        ]);

        // Check email uniqueness if changed
        if (isset($data['email'])) {
            $existing = $this->repository->findWhere(['email' => $data['email']]);
            if (!empty($existing) && $existing[0]->id != $id) {
                throw new ConflictException('Employee', 'Email already in use');
            }
        }

        $success = $this->repository->update($id, $data);

        if ($success) {
            $this->logAction('Employee updated', ['employee_id' => $id]);

            // Invalidate cache
            $this->invalidateOnUpdate($id);
        }

        return $success;
    }

    /**
     * Delete an employee.
     *
     * @param int $id Employee ID
     * @return bool Success
     * @throws NotFoundException|ConflictException
     */
    public function deleteEmployee(int $id): bool
    {
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Employee', $id);
        }

        // Check if employee has active assignments
        $assignmentRepo = RepositoryFactory::assignments();
        $assignments = $assignmentRepo->findByEmployee($id);
        $activeAssignments = array_filter($assignments, fn($a) => $a->return_date === null);

        if (!empty($activeAssignments)) {
            throw new ConflictException('Employee', 'Cannot delete employee with active asset assignments');
        }

        $success = $this->repository->delete($id);

        if ($success) {
            $this->logAction('Employee deleted', ['employee_id' => $id]);

            // Invalidate cache
            $this->invalidateOnDelete($id);
        }

        return $success;
    }

    /**
     * Get employee by ID.
     *
     * @param int $id
     * @return object|null
     */
    public function getEmployee(int $id): ?object
    {
        return $this->remember('get', function () use ($id) {
            return $this->repository->findById($id);
        }, 3600, ['id' => $id]);
    }

    /**
     * List employees with pagination.
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function listEmployees(int $page = 1, int $perPage = 10): array
    {
        return $this->remember('list', function () use ($page, $perPage) {
            $offset = ($page - 1) * $perPage;
            return $this->repository->findAll(limit: $perPage, offset: $offset);
        }, 3600, ['page' => $page, 'perPage' => $perPage]);
    }

    /**
     * Search employees by name or email.
     *
     * @param string $query
     * @return array
     */
    public function searchEmployees(string $query): array
    {
        return $this->repository->search($query);
    }

    /**
     * Get employees by department.
     *
     * @param int $departmentId
     * @return array
     */
    public function getByDepartment(int $departmentId): array
    {
        return $this->repository->findByDepartment($departmentId);
    }

    /**
     * Count total employees.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->repository->getCount();
    }
}
