<?php

use core\Exceptions\ValidationException;

/**
 * Api/VendorController - REST API endpoints for vendors
 */
class Api_VendorController extends ApiController
{
    public function index(): void
    {
        try {
            $this->requireRole('Manager');

            $page = $this->getPage();
            $perPage = $this->getPerPage();
            $search = $this->getSearchQuery();

            $service = ServiceFactory::vendors();

            if ($search) {
                $items = $service->searchVendors($search);
            } else {
                $items = $service->listVendors(page: $page, perPage: $perPage);
            }

            $total = $service->count();
            $this->respondPaginated($items, $total, $page, $perPage, 'Vendors retrieved successfully');
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
                throw new ValidationException('Vendor ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::vendors();
            $vendor = $service->getVendor($id);

            if (!$vendor) {
                $this->respondNotFound('Vendor', $id);
                return;
            }

            $this->respondSuccess($vendor, 'Vendor retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    public function store(): void
    {
        try {
            $this->requireRole('Admin');

            $data = $this->getRequestData();
            $this->validateRequired($data, ['name', 'contact_person', 'email']);

            $service = ServiceFactory::vendors();
            $id = $service->createVendor($data);

            $vendor = $service->getVendor($id);
            $this->respondCreated($vendor, 'Vendor created successfully');
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
                throw new ValidationException('Vendor ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::vendors();
            $success = $service->updateVendor($id, $data);

            if (!$success) {
                $this->respondError('update_failed', 'Failed to update vendor', 500);
                return;
            }

            $vendor = $service->getVendor($id);
            $this->respondSuccess($vendor, 'Vendor updated successfully');
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
                throw new ValidationException('Vendor ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::vendors();
            $success = $service->deleteVendor($id);

            if (!$success) {
                $this->respondError('delete_failed', 'Failed to delete vendor', 500);
                return;
            }

            $this->respondSuccess(null, 'Vendor deleted successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }
}
