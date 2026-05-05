<?php

use core\Exceptions\{ValidationException, ConflictException, NotFoundException};

/**
 * VendorService - Business logic for vendor management
 */
class VendorService extends BaseService
{
    public function __construct()
    {
        parent::__construct(RepositoryFactory::vendors());
    }

    /**
     * Create a new vendor.
     *
     * @param array $data Vendor data
     * @return int Vendor ID
     */
    public function createVendor(array $data): int
    {
        $this->validateRequired($data, ['vendor_name', 'email', 'contact_person']);

        $this->validate($data, [
            'vendor_name' => 'required|min:3',
            'email' => 'required|email',
            'contact_person' => 'required|min:2',
        ]);

        // Check for duplicate vendor name
        if ($this->exists(['vendor_name' => $data['vendor_name']])) {
            throw new ConflictException('Vendor', 'Vendor name already exists');
        }

        $id = $this->repository->create($data);
        $this->logAction('Vendor created', ['vendor_id' => $id, 'name' => $data['vendor_name']]);

        // Invalidate cache
        $this->invalidateOnCreate();

        return $id;
    }

    /**
     * Update vendor information.
     *
     * @param int $id Vendor ID
     * @param array $data Updated data
     * @return bool Success
     */
    public function updateVendor(int $id, array $data): bool
    {
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Vendor', $id);
        }

        $this->validate($data, [
            'vendor_name' => 'min:3',
            'email' => 'email',
            'contact_person' => 'min:2',
        ]);

        $success = $this->repository->update($id, $data);

        if ($success) {
            $this->logAction('Vendor updated', ['vendor_id' => $id]);

            // Invalidate cache
            $this->invalidateOnUpdate($id);
        }

        return $success;
    }

    /**
     * Delete a vendor.
     *
     * @param int $id Vendor ID
     * @return bool Success
     */
    public function deleteVendor(int $id): bool
    {
        if (!$this->repository->exists($id)) {
            throw new NotFoundException('Vendor', $id);
        }

        // Check if vendor has active maintenance contracts
        $maintenanceRepo = RepositoryFactory::maintenance();
        $records = $maintenanceRepo->findByAsset($id);

        if (!empty($records)) {
            throw new ConflictException('Vendor', 'Cannot delete vendor with active maintenance records');
        }

        $success = $this->repository->delete($id);

        if ($success) {
            $this->logAction('Vendor deleted', ['vendor_id' => $id]);

            // Invalidate cache
            $this->invalidateOnDelete($id);
        }

        return $success;
    }

    /**
     * Get vendor by ID.
     *
     * @param int $id
     * @return object|null
     */
    public function getVendor(int $id): ?object
    {
        return $this->remember('get', function () use ($id) {
            return $this->repository->findById($id);
        }, 3600, ['id' => $id]);
    }

    /**
     * List vendors with pagination.
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function listVendors(int $page = 1, int $perPage = 10): array
    {
        return $this->remember('list', function () use ($page, $perPage) {
            $offset = ($page - 1) * $perPage;
            return $this->repository->findAll(limit: $perPage, offset: $offset);
        }, 3600, ['page' => $page, 'perPage' => $perPage]);
    }

    /**
     * Search vendors by name or contact.
     *
     * @param string $query
     * @return array
     */
    public function searchVendors(string $query): array
    {
        return $this->repository->search($query);
    }

    /**
     * Count total vendors.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->repository->getCount();
    }
}
