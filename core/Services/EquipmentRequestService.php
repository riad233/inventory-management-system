<?php

use core\Exceptions\{ValidationException, ConflictException, NotFoundException};

/**
 * EquipmentRequestService - Business logic for equipment requests
 *
 * Handles:
 * - Creating requests
 * - Approving/rejecting requests
 * - Request tracking
 */
class EquipmentRequestService extends BaseService
{
    public function __construct()
    {
        parent::__construct(RepositoryFactory::requests());
    }

    /**
     * Submit an equipment request.
     *
     * @param array $data Request data
     * @return int Request ID
     */
    public function submitRequest(array $data): int
    {
        $this->validateRequired($data, ['employee_id', 'equipment_type', 'description', 'request_date']);

        $this->validate($data, [
            'employee_id' => 'required|integer',
            'equipment_type' => 'required|min:2',
            'description' => 'required|min:5',
            'request_date' => 'required|date',
        ]);

        // Verify employee exists
        if (!RepositoryFactory::employees()->exists($data['employee_id'])) {
            throw new NotFoundException('Employee', $data['employee_id']);
        }

        // Set default status
        $data['status'] = $data['status'] ?? 'pending';

        $id = $this->repository->create($data);
        $this->logAction('Equipment request submitted', [
            'request_id' => $id,
            'employee_id' => $data['employee_id'],
        ]);

        // Invalidate cache
        $this->invalidateOnCreate();

        return $id;
    }

    /**
     * Approve an equipment request.
     *
     * @param int $id Request ID
     * @param string $approvalNotes Notes from approver
     * @return bool Success
     */
    public function approveRequest(int $id, string $approvalNotes = ''): bool
    {
        $request = $this->repository->findById($id);
        if (!$request) {
            throw new NotFoundException('Request', $id);
        }

        // Check if already processed
        if ($request->status !== 'pending') {
            throw new ConflictException('Request', 'Can only approve pending requests');
        }

        $success = $this->repository->update($id, [
            'status' => 'approved',
            'approval_notes' => $approvalNotes,
            'approved_date' => date('Y-m-d'),
            'approved_by' => $_SESSION['user_id'] ?? 'system',
        ]);

        if ($success) {
            $this->logAction('Equipment request approved', ['request_id' => $id]);

            // Invalidate cache
            $this->invalidateOnUpdate($id);
        }

        return $success;
    }

    /**
     * Reject an equipment request.
     *
     * @param int $id Request ID
     * @param string $rejectionReason Reason for rejection
     * @return bool Success
     */
    public function rejectRequest(int $id, string $rejectionReason): bool
    {
        $request = $this->repository->findById($id);
        if (!$request) {
            throw new NotFoundException('Request', $id);
        }

        // Check if already processed
        if ($request->status !== 'pending') {
            throw new ConflictException('Request', 'Can only reject pending requests');
        }

        $this->validate(['rejectionReason' => $rejectionReason], [
            'rejectionReason' => 'required|min:3',
        ]);

        $success = $this->repository->update($id, [
            'status' => 'rejected',
            'rejection_reason' => $rejectionReason,
            'rejected_date' => date('Y-m-d'),
            'rejected_by' => $_SESSION['user_id'] ?? 'system',
        ]);

        if ($success) {
            $this->logAction('Equipment request rejected', ['request_id' => $id]);

            // Invalidate cache
            $this->invalidateOnUpdate($id);
        }

        return $success;
    }

    /**
     * Get request details.
     *
     * @param int $id
     * @return object|null
     */
    public function getRequest(int $id): ?object
    {
        return $this->remember('get', function () use ($id) {
            return $this->repository->findById($id);
        }, 3600, ['id' => $id]);
    }

    /**
     * List requests with pagination.
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function listRequests(int $page = 1, int $perPage = 10): array
    {
        return $this->remember('list', function () use ($page, $perPage) {
            $offset = ($page - 1) * $perPage;
            return $this->repository->findAll(limit: $perPage, offset: $offset);
        }, 3600, ['page' => $page, 'perPage' => $perPage]);
    }

    /**
     * Get pending requests.
     *
     * @return array
     */
    public function getPending(): array
    {
        return $this->repository->getPending();
    }

    /**
     * Get requests by employee.
     *
     * @param int $employeeId
     * @return array
     */
    public function getByEmployee(int $employeeId): array
    {
        return $this->repository->findByEmployee($employeeId);
    }

    /**
     * Count total requests.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->repository->getCount();
    }

    /**
     * Count pending requests.
     *
     * @return int
     */
    public function countPending(): int
    {
        return $this->repository->countPending();
    }
}
