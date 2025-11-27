<?php

namespace Nayemuf\PathaoCourier\Apis;

class AreaApi extends BaseApi
{
    /**
     * Get list of cities
     *
     * @return array
     */
    public function getCities(): array
    {
        return $this->request('GET', '/aladdin/api/v1/city-list');
    }

    /**
     * Get zones for a city
     *
     * @param int $cityId
     * @return array
     */
    public function getZones(int $cityId): array
    {
        return $this->request('GET', "/aladdin/api/v1/cities/{$cityId}/zone-list");
    }

    /**
     * Get areas for a zone
     *
     * @param int $zoneId
     * @return array
     */
    public function getAreas(int $zoneId): array
    {
        return $this->request('GET', "/aladdin/api/v1/zones/{$zoneId}/area-list");
    }
}

