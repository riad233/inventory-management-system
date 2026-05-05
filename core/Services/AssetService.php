<?php

use core\Exceptions\{ValidationException, ConflictException, NotFoundException};

/**
 * AssetService - Business logic for asset management
 *
 * Handles:
 * - Asset creation with validation
 * - Asset updates with business rules
 * - Asset status changes
 * - Availability checking
 */
class AssetService extends BaseService
{
    public function __construct()
    {
        parent::__construct(RepositoryFactory::assets());
    }

    /**
     * Create a new asset with validation.
     *
     * @param array $data Asset data
     * @return int Asset ID
     * @throws ValidationException
     * @throws DatabaseException
     */
    public function createAsset(array $data): int
    {
        // Validate required fields
        $this->validateRequired($data, ['asset_tag', 'description', 'status']);

        // Validate field formats
        $this->validate($data, [
            'asset_tag' => 'required',
            'description' => 'required|min:3',
            'status' => 'required',
        ]);

        // Check for duplicate asset tag
        if ($this->exists(['asset_tag' => $data['asset_tag']])) {
            throw new ConflictException('Asset', 'Asset tag already exists');
        }

        // Create the asset
        $id = $this->repository->create($data);

        // Log the action
        $this->logAction('Asset created', [
            'asset_id' => $id,
            'asset_tag' => $data['asset_tag'],
        ]);

        // Invalidate cache
        $this->invalidateOnCreate();

        return $id;
    }

    /**
     * Update an existing asset.
     *
     * @param int $id Asset ID
     * @param array $data Updated data
     * @return bool Success
     * @throws NotFoundException
     * @throws ValidationException
     * @throws DatabaseException
     */
    public function updateAsset(int $id, array $data): bool
    {
        // Verify asset exists
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Asset', $id);
        }

        // Validate data if provided
        if (!empty($data)) {
            $this->validate($data, [
                'description' => 'min:3',
                'status' => 'required',
            ]);

            // Check if changing asset_tag to a duplicate
            if (isset($data['asset_tag']) && $data['asset_tag'] !== '') {
                $existing = $this->repository->findWhere(['asset_tag' => $data['asset_tag']]);
                if (!empty($existing) && $existing[0]->id != $id) {
                    throw new ConflictException('Asset', 'Asset tag already exists');
                }
            }
        }

        // Update the asset
        $success = $this->repository->update($id, $data);

        if ($success) {
            $this->logAction('Asset updated', [
                'asset_id' => $id,
                'changes' => count($data),
            ]);

            // Invalidate cache
            $this->invalidateOnUpdate($id);
        }

        return $success;
    }

    /**
     * Delete an asset.
     *
     * @param int $id Asset ID
     * @return bool Success
     * @throws NotFoundException
     * @throws DatabaseException
     */
    public function deleteAsset(int $id): bool
    {
        // Verify asset exists
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Asset', $id);
        }

        // Check if asset is assigned (business rule)
        $assignmentRepo = RepositoryFactory::assignments();
        $activeAssignments = $assignmentRepo->findByAsset($id);
        $activeAssignments = array_filter($activeAssignments, function($a) {
            return $a->return_date === null;
        });

        if (!empty($activeAssignments)) {
            throw new ConflictException('Asset', 'Cannot delete asset that is currently assigned');
        }

        // Delete the asset
        $success = $this->repository->delete($id);

        if ($success) {
            $this->logAction('Asset deleted', ['asset_id' => $id]);

            // Invalidate cache
            $this->invalidateOnDelete($id);
        }

        return $success;
    }

    /**
     * Get asset details.
     *
     * @param int $id Asset ID
     * @return object|null Asset instance
     * @throws DatabaseException
     */
    public function getAsset(int $id): ?object
    {
        return $this->remember('get', function () use ($id) {
            return $this->repository->findById($id);
        }, 3600, ['id' => $id]);
    }

    /**
     * Get all assets with pagination.
     *
     * @param int $page Page number (1-indexed)
     * @param int $perPage Results per page
     * @return array Assets for the page
     * @throws DatabaseException
     */
    public function listAssets(int $page = 1, int $perPage = 10): array
    {
        return $this->remember('list', function () use ($page, $perPage) {
            $offset = ($page - 1) * $perPage;
            return $this->repository->findAll(limit: $perPage, offset: $offset);
        }, 3600, ['page' => $page, 'perPage' => $perPage]);
    }

    /**
     * Get available assets for assignment.
     *
     * @return array Available assets
     * @throws DatabaseException
     */
    public function getAvailableAssets(): array
    {
        return $this->repository->getAvailable();
    }

    /**
     * Change asset status.
     *
     * @param int $id Asset ID
     * @param string $status New status
     * @return bool Success
     * @throws NotFoundException
     * @throws DatabaseException
     */
    public function changeStatus(int $id, string $status): bool
    {
        // Verify asset exists
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Asset', $id);
        }

        $valid_statuses = ['active', 'inactive', 'retired', 'maintenance'];
        if (!in_array($status, $valid_statuses)) {
            throw new ValidationException(['status' => 'Invalid status value']);
        }

        $success = $this->repository->update($id, ['status' => $status]);

        if ($success) {
            $this->logAction('Asset status changed', [
                'asset_id' => $id,
                'new_status' => $status,
            ]);

            // Invalidate cache
            $this->invalidateOnUpdate($id);
        }

        return $success;
    }

    /**
     * Count total assets.
     *
     * @return int
     * @throws DatabaseException
     */
    public function count(): int
    {
        return $this->repository->getCount();
    }

    /**
     * Get asset count by status.
     *
     * @param string $status
     * @return int
     * @throws DatabaseException
     */
    public function countByStatus(string $status): int
    {
        return $this->repository->countByStatus($status);
    }
}
