<?php

use core\Exceptions\ValidationException;

/**
 * Api/EquipmentRequestController - REST API endpoints for equipment requests
 */
class Api_EquipmentRequestController extends ApiController
{
    public function index(): void
    {
        try {
            $this->requireRole('Manager');

            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $service = ServiceFactory::requests();
            $items = $service->listRequests(page: $page, perPage: $perPage);
            $total = $service->count();

            $this->respondPaginated($items, $total, $page, $perPage, 'Equipment requests retrieved successfully');
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
                throw new ValidationException('Request ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::requests();
            $request = $service->getRequest($id);

            if (!$request) {
                $this->respondNotFound('Request', $id);
                return;
            }

            $this->respondSuccess($request, 'Request retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function store(): void
    {
        try {
            $this->requireAuth();

            $data = $this->getRequestData();
            $this->validateRequired($data, ['equipment_type', 'description']);

            // Use current user if not specified
            if (!isset($data['employee_id'])) {
                $data['employee_id'] = $_SESSION['user_id'];
            }

            $service = ServiceFactory::requests();
            $id = $service->submitRequest($data);

            $request = $service->getRequest($id);
            $this->respondCreated($request, 'Request submitted successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function approve(): void
    {
        try {
            $this->requireRole('Admin');

            $id = (int) $this->getParam('id');
            $data = $this->getRequestData();

            if (!$id) {
                throw new ValidationException('Request ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::requests();
            $success = $service->approveRequest($id, $data['approval_notes'] ?? '');

            if (!$success) {
                $this->respondError('approval_failed', 'Failed to approve request', 500);
                return;
            }

            $request = $service->getRequest($id);
            $this->respondSuccess($request, 'Request approved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function reject(): void
    {
        try {
            $this->requireRole('Admin');

            $id = (int) $this->getParam('id');
            $data = $this->getRequestData();

            if (!$id) {
                throw new ValidationException('Request ID required', ['id' => 'ID is required']);
            }

            $this->validateRequired($data, ['rejection_reason']);

            $service = ServiceFactory::requests();
            $success = $service->rejectRequest($id, $data['rejection_reason']);

            if (!$success) {
                $this->respondError('rejection_failed', 'Failed to reject request', 500);
                return;
            }

            $request = $service->getRequest($id);
            $this->respondSuccess($request, 'Request rejected successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function pending(): void
    {
        try {
            $this->requireRole('Manager');

            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $service = ServiceFactory::requests();
            $items = $service->getPending();
            $total = $service->countPending();

            // Manual pagination
            $items = array_slice($items, ($page - 1) * $perPage, $perPage);

            $this->respondPaginated($items, $total, $page, $perPage, 'Pending requests retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function byEmployee(): void
    {
        try {
            $this->requireAuth();

            $empId = (int) $this->getParam('employee_id');
            if (!$empId) {
                throw new ValidationException('Employee ID required', ['employee_id' => 'Employee ID is required']);
            }

            // Non-admin can only view their own requests
            if (!AuthorizationHelper::isSuperAdmin() && $_SESSION['user_id'] != $empId) {
                throw new AuthorizationException('insufficient_permissions', 'Manager');
            }

            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $service = ServiceFactory::requests();
            $items = $service->getByEmployee($empId);
            $total = count($items);

            // Manual pagination
            $items = array_slice($items, ($page - 1) * $perPage, $perPage);

            $this->respondPaginated($items, $total, $page, $perPage, 'Requests retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }
}
