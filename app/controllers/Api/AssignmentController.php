<?php

use core\Exceptions\ValidationException;

/**
 * Api/AssignmentController - REST API endpoints for assignments
 *
 * Endpoints:
 * - GET /api/assignments - List all assignments
 * - GET /api/assignments/:id - Get assignment details
 * - POST /api/assignments - Assign asset
 * - PUT /api/assignments/:id - Update assignment
 * - DELETE /api/assignments/:id - Delete assignment
 * - POST /api/assignments/:id/return - Return asset
 * - GET /api/assignments/pending - Get pending assignments
 */
class Api_AssignmentController extends ApiController
{
    public function index(): void
    {
        try {
            $this->requireRole('Manager');

            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $service = ServiceFactory::assignments();
            $items = $service->listAssignments(page: $page, perPage: $perPage);
            $total = $service->count();

            $this->respondPaginated($items, $total, $page, $perPage, 'Assignments retrieved successfully');
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
                throw new ValidationException('Assignment ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::assignments();
            $assignment = $service->getAssignment($id);

            if (!$assignment) {
                $this->respondNotFound('Assignment', $id);
                return;
            }

            $this->respondSuccess($assignment, 'Assignment retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function store(): void
    {
        try {
            $this->requireRole('Manager');

            $data = $this->getRequestData();
            $this->validateRequired($data, ['asset_id', 'employee_id', 'assignment_date']);

            $service = ServiceFactory::assignments();
            $id = $service->assignAsset($data);

            $assignment = $service->getAssignment($id);
            $this->respondCreated($assignment, 'Asset assigned successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function update(): void
    {
        try {
            $this->requireRole('Manager');

            $id = (int) $this->getParam('id');
            $data = $this->getRequestData();

            if (!$id) {
                throw new ValidationException('Assignment ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::assignments();
            $success = $service->updateAssignment($id, $data);

            if (!$success) {
                $this->respondError('update_failed', 'Failed to update assignment', 500);
                return;
            }

            $assignment = $service->getAssignment($id);
            $this->respondSuccess($assignment, 'Assignment updated successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function destroy(): void
    {
        try {
            $this->requireRole('Manager');

            $id = (int) $this->getParam('id');
            if (!$id) {
                throw new ValidationException('Assignment ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::assignments();
            $success = $service->deleteAssignment($id);

            if (!$success) {
                $this->respondError('delete_failed', 'Failed to delete assignment', 500);
                return;
            }

            $this->respondSuccess(null, 'Assignment deleted successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function returnAsset(): void
    {
        try {
            $this->requireRole('Manager');

            $id = (int) $this->getParam('id');
            $data = $this->getRequestData();

            if (!$id) {
                throw new ValidationException('Assignment ID required', ['id' => 'ID is required']);
            }

            $this->validateRequired($data, ['return_date']);

            $service = ServiceFactory::assignments();
            $success = $service->returnAsset($id, $data['return_date']);

            if (!$success) {
                $this->respondError('return_failed', 'Failed to return asset', 500);
                return;
            }

            $assignment = $service->getAssignment($id);
            $this->respondSuccess($assignment, 'Asset returned successfully');
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

            $service = ServiceFactory::assignments();
            $items = $service->getPending();
            $total = $service->countPending();

            // Manual pagination
            $items = array_slice($items, ($page - 1) * $perPage, $perPage);

            $this->respondPaginated($items, $total, $page, $perPage, 'Pending assignments retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }
}
