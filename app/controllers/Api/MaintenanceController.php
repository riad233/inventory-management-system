<?php

use core\Exceptions\ValidationException;

/**
 * Api/MaintenanceController - REST API endpoints for maintenance
 */
class Api_MaintenanceController extends ApiController
{
    public function index(): void
    {
        try {
            $this->requireRole('Manager');

            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $service = ServiceFactory::maintenance();
            $items = $service->listMaintenance(page: $page, perPage: $perPage);
            $total = $service->count();

            $this->respondPaginated($items, $total, $page, $perPage, 'Maintenance records retrieved successfully');
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
                throw new ValidationException('Maintenance ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::maintenance();
            $maintenance = $service->getMaintenance($id);

            if (!$maintenance) {
                $this->respondNotFound('Maintenance', $id);
                return;
            }

            $this->respondSuccess($maintenance, 'Maintenance retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function store(): void
    {
        try {
            $this->requireRole('Manager');

            $data = $this->getRequestData();
            $this->validateRequired($data, ['asset_id', 'scheduled_date', 'description']);

            $service = ServiceFactory::maintenance();
            $id = $service->scheduleMaintenance($data);

            $maintenance = $service->getMaintenance($id);
            $this->respondCreated($maintenance, 'Maintenance scheduled successfully');
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
                throw new ValidationException('Maintenance ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::maintenance();
            $success = $service->updateMaintenance($id, $data);

            if (!$success) {
                $this->respondError('update_failed', 'Failed to update maintenance', 500);
                return;
            }

            $maintenance = $service->getMaintenance($id);
            $this->respondSuccess($maintenance, 'Maintenance updated successfully');
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
                throw new ValidationException('Maintenance ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::maintenance();
            $success = $service->deleteMaintenance($id);

            if (!$success) {
                $this->respondError('delete_failed', 'Failed to delete maintenance', 500);
                return;
            }

            $this->respondSuccess(null, 'Maintenance deleted successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function complete(): void
    {
        try {
            $this->requireRole('Manager');

            $id = (int) $this->getParam('id');
            $data = $this->getRequestData();

            if (!$id) {
                throw new ValidationException('Maintenance ID required', ['id' => 'ID is required']);
            }

            $this->validateRequired($data, ['completed_date']);

            $service = ServiceFactory::maintenance();
            $success = $service->completeMaintenance($id, $data['completed_date'], $data['notes'] ?? '');

            if (!$success) {
                $this->respondError('complete_failed', 'Failed to complete maintenance', 500);
                return;
            }

            $maintenance = $service->getMaintenance($id);
            $this->respondSuccess($maintenance, 'Maintenance completed successfully');
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

            $service = ServiceFactory::maintenance();
            $items = $service->getPending();
            $total = $service->countPending();

            // Manual pagination
            $items = array_slice($items, ($page - 1) * $perPage, $perPage);

            $this->respondPaginated($items, $total, $page, $perPage, 'Pending maintenance retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }
}
