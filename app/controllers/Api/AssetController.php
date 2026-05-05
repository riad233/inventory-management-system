<?php

use core\Exceptions\ValidationException;

/**
 * Api/AssetController - REST API endpoints for assets
 *
 * Endpoints:
 * - GET /api/assets - List all assets
 * - GET /api/assets/:id - Get asset details
 * - POST /api/assets - Create asset
 * - PUT /api/assets/:id - Update asset
 * - DELETE /api/assets/:id - Delete asset
 * - GET /api/assets/available - Get available assets
 * - PUT /api/assets/:id/status - Change status
 */
class Api_AssetController extends ApiController
{
    /**
     * List all assets with pagination.
     */
    public function index(): void
    {
        try {
            $this->requireRole('Manager');

            $page = $this->getPage();
            $perPage = $this->getPerPage();
            $search = $this->getSearchQuery();

            $service = ServiceFactory::assets();

            if ($search) {
                // Search implementation (added as custom method if needed)
                $items = $service->listAssets(page: $page, perPage: $perPage);
            } else {
                $items = $service->listAssets(page: $page, perPage: $perPage);
            }

            $total = $service->count();

            $this->respondPaginated($items, $total, $page, $perPage, 'Assets retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Get single asset.
     */
    public function show(): void
    {
        try {
            $this->requireRole('Manager');

            $id = (int) $this->getParam('id');
            if (!$id) {
                throw new ValidationException('Asset ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::assets();
            $asset = $service->getAsset($id);

            if (!$asset) {
                $this->respondNotFound('Asset', $id);
                return;
            }

            $this->respondSuccess($asset, 'Asset retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Create new asset.
     */
    public function store(): void
    {
        try {
            $this->requireRole('Admin');

            $data = $this->getRequestData();
            $this->validateRequired($data, ['asset_tag', 'description', 'status']);

            $service = ServiceFactory::assets();
            $id = $service->createAsset($data);

            $asset = $service->getAsset($id);
            $this->respondCreated($asset, 'Asset created successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Update asset.
     */
    public function update(): void
    {
        try {
            $this->requireRole('Admin');

            $id = (int) $this->getParam('id');
            $data = $this->getRequestData();

            if (!$id) {
                throw new ValidationException('Asset ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::assets();
            $success = $service->updateAsset($id, $data);

            if (!$success) {
                $this->respondError('update_failed', 'Failed to update asset', 500);
                return;
            }

            $asset = $service->getAsset($id);
            $this->respondSuccess($asset, 'Asset updated successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Delete asset.
     */
    public function destroy(): void
    {
        try {
            $this->requireRole('Admin');

            $id = (int) $this->getParam('id');
            if (!$id) {
                throw new ValidationException('Asset ID required', ['id' => 'ID is required']);
            }

            $service = ServiceFactory::assets();
            $success = $service->deleteAsset($id);

            if (!$success) {
                $this->respondError('delete_failed', 'Failed to delete asset', 500);
                return;
            }

            $this->respondSuccess(null, 'Asset deleted successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Get available assets (not assigned).
     */
    public function available(): void
    {
        try {
            $this->requireRole('Manager');

            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $service = ServiceFactory::assets();
            $items = $service->getAvailableAssets();

            // Manual pagination for available assets
            $total = count($items);
            $items = array_slice($items, ($page - 1) * $perPage, $perPage);

            $this->respondPaginated($items, $total, $page, $perPage, 'Available assets retrieved successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Change asset status.
     */
    public function changeStatus(): void
    {
        try {
            $this->requireRole('Admin');

            $id = (int) $this->getParam('id');
            $data = $this->getRequestData();

            if (!$id) {
                throw new ValidationException('Asset ID required', ['id' => 'ID is required']);
            }

            $this->validateRequired($data, ['status']);

            $service = ServiceFactory::assets();
            $success = $service->changeStatus($id, $data['status']);

            if (!$success) {
                $this->respondError('status_change_failed', 'Failed to change asset status', 500);
                return;
            }

            $asset = $service->getAsset($id);
            $this->respondSuccess($asset, 'Asset status changed successfully');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }
}
