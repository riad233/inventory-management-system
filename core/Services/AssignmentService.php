<?php

use core\Exceptions\{ValidationException, ConflictException, NotFoundException};

/**
 * AssignmentService - Business logic for asset assignments
 *
 * Handles:
 * - Creating assignments (with business rules)
 * - Returning assets
 * - Assignment tracking
 */
class AssignmentService extends BaseService
{
    public function __construct()
    {
        parent::__construct(RepositoryFactory::assignments());
    }

    /**
     * Create a new asset assignment.
     *
     * @param array $data Assignment data
     * @return int Assignment ID
     */
    public function assignAsset(array $data): int
    {
        $this->validateRequired($data, ['asset_id', 'employee_id', 'assignment_date']);

        $this->validate($data, [
            'asset_id' => 'required|integer',
            'employee_id' => 'required|integer',
            'assignment_date' => 'required|date',
        ]);

        // Verify asset exists and is available
        $assetRepo = RepositoryFactory::assets();
        $asset = $assetRepo->findById($data['asset_id']);
        if (!$asset) {
            throw new NotFoundException('Asset', $data['asset_id']);
        }

        // Check if asset is already assigned
        $activeAssignments = $this->repository->findByAsset($data['asset_id']);
        $activeAssignments = array_filter($activeAssignments, fn($a) => $a->return_date === null);
        if (!empty($activeAssignments)) {
            throw new ConflictException('Asset', 'Asset is already assigned to another employee');
        }

        // Verify employee exists
        $employeeRepo = RepositoryFactory::employees();
        if (!$employeeRepo->exists($data['employee_id'])) {
            throw new NotFoundException('Employee', $data['employee_id']);
        }

        // Create assignment within transaction
        $id = $this->transaction(function() use ($data) {
            $id = $this->repository->create($data);
            return $id;
        });

        $this->logAction('Asset assigned', [
            'assignment_id' => $id,
            'asset_id' => $data['asset_id'],
            'employee_id' => $data['employee_id'],
        ]);

        // Invalidate cache
        $this->invalidateOnCreate();

        return $id;
    }

    /**
     * Return an assigned asset.
     *
     * @param int $id Assignment ID
     * @param string $returnDate Return date
     * @param string $returnNotes Optional notes
     * @return bool Success
     */
    public function returnAsset(int $id, string $returnDate, string $returnNotes = ''): bool
    {
        $assignment = $this->repository->findById($id);
        if (!$assignment) {
            throw new NotFoundException('Assignment', $id);
        }

        // Check if already returned
        if ($assignment->return_date !== null) {
            throw new ConflictException('Assignment', 'Asset has already been returned');
        }

        // Validate return date
        $this->validate(['returnDate' => $returnDate], ['returnDate' => 'required|date']);

        // Update with return information
        $success = $this->transaction(function() use ($id, $returnDate, $returnNotes) {
            return $this->repository->update($id, [
                'return_date' => $returnDate,
                'return_notes' => $returnNotes,
            ]);
        });

        if ($success) {
            $this->logAction('Asset returned', [
                'assignment_id' => $id,
                'return_date' => $returnDate,
            ]);

            // Invalidate cache
            $this->invalidateOnUpdate($id);
        }

        return $success;
    }

    /**
     * Update assignment information.
     *
     * @param int $id Assignment ID
     * @param array $data Updated data
     * @return bool Success
     */
    public function updateAssignment(int $id, array $data): bool
    {
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Assignment', $id);
        }

        $this->validate($data, [
            'assignment_date' => 'date',
            'return_date' => 'date',
        ]);

        $success = $this->repository->update($id, $data);

        if ($success) {
            $this->logAction('Assignment updated', ['assignment_id' => $id]);

            // Invalidate cache
            $this->invalidateOnUpdate($id);
        }

        return $success;
    }

    /**
     * Delete an assignment.
     *
     * @param int $id Assignment ID
     * @return bool Success
     */
    public function deleteAssignment(int $id): bool
    {
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Assignment', $id);
        }

        $success = $this->repository->delete($id);

        if ($success) {
            $this->logAction('Assignment deleted', ['assignment_id' => $id]);

            // Invalidate cache
            $this->invalidateOnDelete($id);
        }

        return $success;
    }

    /**
     * Get assignment details.
     *
     * @param int $id
     * @return object|null
     */
    public function getAssignment(int $id): ?object
    {
        return $this->remember('get', function () use ($id) {
            return $this->repository->findById($id);
        }, 3600, ['id' => $id]);
    }

    /**
     * List assignments with pagination.
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function listAssignments(int $page = 1, int $perPage = 10): array
    {
        return $this->remember('list', function () use ($page, $perPage) {
            $offset = ($page - 1) * $perPage;
            return $this->repository->findAll(limit: $perPage, offset: $offset);
        }, 3600, ['page' => $page, 'perPage' => $perPage]);
    }

    /**
     * Get pending assignments (not yet returned).
     *
     * @return array
     */
    public function getPending(): array
    {
        return $this->repository->getPending();
    }

    /**
     * Count total assignments.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->repository->getCount();
    }

    /**
     * Count pending assignments.
     *
     * @return int
     */
    public function countPending(): int
    {
        return $this->repository->getPendingCount();
    }
}
