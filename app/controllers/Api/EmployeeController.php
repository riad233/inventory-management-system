<?php

use core\Exceptions\ValidationException;

/**
 * Api/EmployeeController - REST API endpoints for employees
 *
 * Endpoints:
 * - GET /api/employees - List all employees
 * - GET /api/employees/:id - Get employee details
 * - POST /api/employees - Create employee
 * - PUT /api/employees/:id - Update employee
 * - DELETE /api/employees/:id - Delete employee
 * - GET /api/employees/department/:dept_id - Get by department
 */
class Api_EmployeeController extends ApiController
{
    public function index(): void
    {
        try {
            $this->requireRole('Manager');

            $page = $this->getPage();
            $perPage = $this->getPerPage();
            $search = $this->getSearchQuery();

            $service = ServiceFactory::employees();

            if ($search) {
                $items = $service->searchEmployees($search);
            } else {
                $items = $service->listEmployees(page: $page, perPage: $perPage);
            }

            $total = $service->count();
            $this->respondPaginated($items, $total, $page, $perPage, 'Employees retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function show(): void
    {
        try {
            $this->requireRole('Manager');

            $id = (int) $this->getParam('id');
            if (!$id) {
                throw new ValidationException('Employee ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::employees();
            $employee = $service->getEmployee($id);

            if (!$employee) {
                $this->respondNotFound('Employee', $id);
                return;
            }

            $this->respondSuccess($employee, 'Employee retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function store(): void
    {
        try {
            $this->requireRole('Admin');

            $data = $this->getRequestData();
            $this->validateRequired($data, ['name', 'email', 'department_id']);

            $service = ServiceFactory::employees();
            $id = $service->createEmployee($data);

            $employee = $service->getEmployee($id);
            $this->respondCreated($employee, 'Employee created successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function update(): void
    {
        try {
            $this->requireRole('Admin');

            $id = (int) $this->getParam('id');
            $data = $this->getRequestData();

            if (!$id) {
                throw new ValidationException('Employee ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::employees();
            $success = $service->updateEmployee($id, $data);

            if (!$success) {
                $this->respondError('update_failed', 'Failed to update employee', 500);
                return;
            }

            $employee = $service->getEmployee($id);
            $this->respondSuccess($employee, 'Employee updated successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function destroy(): void
    {
        try {
            $this->requireRole('Admin');

            $id = (int) $this->getParam('id');
            if (!$id) {
                throw new ValidationException('Employee ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::employees();
            $success = $service->deleteEmployee($id);

            if (!$success) {
                $this->respondError('delete_failed', 'Failed to delete employee', 500);
                return;
            }

            $this->respondSuccess(null, 'Employee deleted successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function byDepartment(): void
    {
        try {
            $this->requireRole('Manager');

            $deptId = (int) $this->getParam('dept_id');
            if (!$deptId) {
                throw new ValidationException('Department ID required', ['dept_id' => 'Department ID is required']);
            }

            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $service = ServiceFactory::employees();
            $items = $service->getByDepartment($deptId);

            // Manual pagination
            $total = count($items);
            $items = array_slice($items, ($page - 1) * $perPage, $perPage);

            $this->respondPaginated($items, $total, $page, $perPage, 'Employees retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }
}
