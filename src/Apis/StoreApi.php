<?php

namespace Nayemuf\PathaoCourier\Apis;

use Nayemuf\PathaoCourier\Exceptions\PathaoException;

class StoreApi extends BaseApi
{
    /**
     * Create a new store
     *
     * @param array $storeData
     * @return array
     * @throws PathaoException
     */
    public function create(array $storeData): array
    {
        $this->validateStoreData($storeData);

        return $this->request('POST', '/aladdin/api/v1/stores', $storeData);
    }

    /**
     * Get list of stores
     *
     * @return array
     */
    public function list(): array
    {
        return $this->request('GET', '/aladdin/api/v1/stores');
    }

    /**
     * Get merchant store info (single store details)
     *
     * @param int $storeId
     * @return array
     * @throws PathaoException
     */
    public function getInfo(int $storeId): array
    {
        return $this->request('GET', "/aladdin/api/v1/stores/{$storeId}");
    }

    /**
     * Validate store data
     *
     * @param array $storeData
     * @return void
     * @throws PathaoException
     */
    protected function validateStoreData(array $storeData): void
    {
        $required = ['name', 'contact_name', 'contact_number', 'address', 'city_id', 'zone_id', 'area_id'];

        foreach ($required as $field) {
            if (!isset($storeData[$field])) {
                throw new PathaoException("Required field '{$field}' is missing");
            }
        }

        // Validate name (3-50 characters)
        if (strlen($storeData['name']) < 3 || strlen($storeData['name']) > 50) {
            throw new PathaoException('Store name must be between 3 and 50 characters');
        }

        // Validate contact_name (3-50 characters)
        if (strlen($storeData['contact_name']) < 3 || strlen($storeData['contact_name']) > 50) {
            throw new PathaoException('Contact name must be between 3 and 50 characters');
        }

        // Validate contact_number (11 characters)
        $phone = preg_replace('/[^0-9]/', '', $storeData['contact_number']);
        if (strlen($phone) !== 11) {
            throw new PathaoException('Contact number must be exactly 11 characters');
        }

        // Validate address (15-120 characters)
        if (strlen($storeData['address']) < 15 || strlen($storeData['address']) > 120) {
            throw new PathaoException('Address must be between 15 and 120 characters');
        }
    }
}

