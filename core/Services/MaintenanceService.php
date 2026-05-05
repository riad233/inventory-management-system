<?php

use core\Exceptions\{ValidationException, NotFoundException};

/**
 * MaintenanceService - Business logic for maintenance operations
 */
class MaintenanceService extends BaseService
{
    public function __construct()
    {
        parent::__construct(RepositoryFactory::maintenance());
    }

    /**
     * Schedule maintenance.
     *
     * @param array $data Maintenance data
     * @return int Maintenance ID
     */
    public function scheduleMaintenance(array $data): int
    {
        $this->validateRequired($data, ['asset_id', 'scheduled_date', 'description']);

        $this->validate($data, [
            'asset_id' => 'required|integer',
            'scheduled_date' => 'required|date|future',
            'description' => 'required|min:5',
        ]);

        // Verify asset exists
        if (!RepositoryFactory::assets()->exists($data['asset_id'])) {
            throw new NotFoundException('Asset', $data['asset_id']);
        }

        $id = $this->repository->create($data);
        $this->logAction('Maintenance scheduled', [
            'maintenance_id' => $id,
            'asset_id' => $data['asset_id'],
        ]);

        // Invalidate cache
        $this->invalidateOnCreate();

        return $id;
    }

    /**
     * Complete maintenance.
     *
     * @param int $id Maintenance ID
     * @param string $completionDate Completion date
     * @param string $notes Completion notes
     * @return bool Success
     */
    public function completeMaintenance(int $id, string $completionDate, string $notes = ''): bool
    {
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Maintenance', $id);
        }

        $this->validate(['completionDate' => $completionDate], [
            'completionDate' => 'required|date|past',
        ]);

        $success = $this->repository->update($id, [
            'completion_date' => $completionDate,
            'status' => 'completed',
            'notes' => $notes,
        ]);

        if ($success) {
            $this->logAction('Maintenance completed', ['maintenance_id' => $id]);

            // Invalidate cache
            $this->invalidateOnUpdate($id);
        }

        return $success;
    }

    /**
     * Update maintenance record.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateMaintenance(int $id, array $data): bool
    {
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Maintenance', $id);
        }

        $this->validate($data, [
            'scheduled_date' => 'date',
            'completion_date' => 'date',
            'description' => 'min:5',
        ]);

        $success = $this->repository->update($id, $data);

        if ($success) {
            $this->logAction('Maintenance updated', ['maintenance_id' => $id]);

            // Invalidate cache
            $this->invalidateOnUpdate($id);
        }

        return $success;
    }

    /**
     * Delete maintenance record.
     *
     * @param int $id
     * @return bool
     */
    public function deleteMaintenance(int $id): bool
    {
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Maintenance', $id);
        }

        $success = $this->repository->delete($id);

        if ($success) {
            $this->logAction('Maintenance deleted', ['maintenance_id' => $id]);

            // Invalidate cache
            $this->invalidateOnDelete($id);
        }

        return $success;
    }

    /**
     * Get maintenance details.
     *
     * @param int $id
     * @return object|null
     */
    public function getMaintenance(int $id): ?object
    {
        return $this->remember('get', function () use ($id) {
            return $this->repository->findById($id);
        }, 3600, ['id' => $id]);
    }

    /**
     * List maintenance records.
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function listMaintenance(int $page = 1, int $perPage = 10): array
    {
        return $this->remember('list', function () use ($page, $perPage) {
            $offset = ($page - 1) * $perPage;
            return $this->repository->findAll(limit: $perPage, offset: $offset);
        }, 3600, ['page' => $page, 'perPage' => $perPage]);
    }

    /**
     * Get pending maintenance records.
     *
     * @return array
     */
    public function getPending(): array
    {
        return $this->repository->getPending();
    }

    /**
     * Get maintenance for an asset.
     *
     * @param int $assetId
     * @return array
     */
    public function getForAsset(int $assetId): array
    {
        return $this->repository->findByAsset($assetId);
    }

    /**
     * Count pending maintenance.
     *
     * @return int
     */
    public function countPending(): int
    {
        return $this->repository->getPendingCount();
    }
}
