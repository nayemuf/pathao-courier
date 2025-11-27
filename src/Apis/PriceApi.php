<?php

namespace Nayemuf\PathaoCourier\Apis;

use Nayemuf\PathaoCourier\Exceptions\PathaoException;

class PriceApi extends BaseApi
{
    /**
     * @var string|null
     */
    protected $storeId;

    /**
     * PriceApi constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $username
     * @param string $password
     * @param bool $sandbox
     * @param string|null $storeId
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $username,
        string $password,
        bool $sandbox = false,
        ?string $storeId = null
    ) {
        parent::__construct($clientId, $clientSecret, $username, $password, $sandbox);
        $this->storeId = $storeId;
    }

    /**
     * Calculate price
     *
     * @param array $priceData
     * @return array
     * @throws PathaoException
     */
    public function calculate(array $priceData): array
    {
        // Use default store_id if not provided
        if (!isset($priceData['store_id']) && $this->storeId) {
            $priceData['store_id'] = $this->storeId;
        }

        $this->validatePriceData($priceData);

        return $this->request('POST', '/aladdin/api/v1/merchant/price-plan', $priceData);
    }

    /**
     * Validate price calculation data
     *
     * @param array $priceData
     * @return void
     * @throws PathaoException
     */
    protected function validatePriceData(array $priceData): void
    {
        $required = ['store_id', 'item_type', 'delivery_type', 'item_weight', 'recipient_city', 'recipient_zone'];

        foreach ($required as $field) {
            if (!isset($priceData[$field])) {
                throw new PathaoException("Required field '{$field}' is missing");
            }
        }

        // Validate item_weight (0.5 - 10 kg)
        $weight = (float) $priceData['item_weight'];
        if ($weight < 0.5 || $weight > 10) {
            throw new PathaoException('item_weight must be between 0.5 and 10 kg');
        }

        // Validate delivery_type
        if (!in_array($priceData['delivery_type'], [48, 12])) {
            throw new PathaoException('delivery_type must be 48 (Normal) or 12 (On Demand)');
        }

        // Validate item_type
        if (!in_array($priceData['item_type'], [1, 2])) {
            throw new PathaoException('item_type must be 1 (Document) or 2 (Parcel)');
        }
    }
}

