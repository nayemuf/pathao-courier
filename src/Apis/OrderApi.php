<?php

namespace Nayemuf\PathaoCourier\Apis;

use Nayemuf\PathaoCourier\Exceptions\PathaoException;

class OrderApi extends BaseApi
{
    /**
     * Create a new order
     *
     * @param array $orderData
     * @return array
     * @throws PathaoException
     */
    public function create(array $orderData): array
    {
        $this->validateOrderData($orderData);

        return $this->request('POST', '/aladdin/api/v1/orders', $orderData);
    }

    /**
     * Create bulk orders
     *
     * @param array $orders
     * @return array
     * @throws PathaoException
     */
    public function createBulk(array $orders): array
    {
        foreach ($orders as $order) {
            $this->validateOrderData($order);
        }

        return $this->request('POST', '/aladdin/api/v1/orders/bulk', ['orders' => $orders]);
    }

    /**
     * Get order short info
     *
     * @param string $consignmentId
     * @return array
     * @throws PathaoException
     */
    public function getInfo(string $consignmentId): array
    {
        return $this->request('GET', "/aladdin/api/v1/orders/{$consignmentId}/info");
    }

    /**
     * Get order details
     *
     * @param string $consignmentId
     * @return array
     * @throws PathaoException
     */
    public function getDetails(string $consignmentId): array
    {
        return $this->request('GET', "/aladdin/api/v1/orders/{$consignmentId}");
    }

    /**
     * Validate order data
     *
     * @param array $orderData
     * @return void
     * @throws PathaoException
     */
    protected function validateOrderData(array $orderData): void
    {
        $required = ['store_id', 'recipient_name', 'recipient_phone', 'recipient_address', 'delivery_type', 'item_type', 'item_quantity', 'item_weight', 'amount_to_collect'];

        foreach ($required as $field) {
            if (!isset($orderData[$field])) {
                throw new PathaoException("Required field '{$field}' is missing");
            }
        }

        // Validate recipient_name (3-100 characters)
        if (strlen($orderData['recipient_name']) < 3 || strlen($orderData['recipient_name']) > 100) {
            throw new PathaoException('recipient_name must be between 3 and 100 characters');
        }

        // Validate recipient_phone (11 characters)
        $phone = preg_replace('/[^0-9]/', '', $orderData['recipient_phone']);
        if (strlen($phone) !== 11) {
            throw new PathaoException('recipient_phone must be exactly 11 characters');
        }

        // Validate recipient_address (10-220 characters)
        if (strlen($orderData['recipient_address']) < 10 || strlen($orderData['recipient_address']) > 220) {
            throw new PathaoException('recipient_address must be between 10 and 220 characters');
        }

        // Validate item_weight (0.5 - 10 kg)
        $weight = (float) $orderData['item_weight'];
        if ($weight < 0.5 || $weight > 10) {
            throw new PathaoException('item_weight must be between 0.5 and 10 kg');
        }

        // Validate delivery_type
        if (!in_array($orderData['delivery_type'], [48, 12])) {
            throw new PathaoException('delivery_type must be 48 (Normal) or 12 (On Demand)');
        }

        // Validate item_type
        if (!in_array($orderData['item_type'], [1, 2])) {
            throw new PathaoException('item_type must be 1 (Document) or 2 (Parcel)');
        }
    }
}

