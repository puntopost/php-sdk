<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request;

use DateTimeImmutable;

class ListMerchantParcelsRequest
{
    private string $merchantId;
    private ?DateTimeImmutable $dateMin;
    private ?DateTimeImmutable $dateMax;
    /** @var string[] */
    private array $statuses;
    private ?string $query;
    private ?int $limit;
    private ?int $offset;

    /**
     * @param string[] $statuses
     */
    public function __construct(
        string $merchantId,
        ?DateTimeImmutable $dateMin = null,
        ?DateTimeImmutable $dateMax = null,
        array $statuses = [],
        ?string $query = null,
        ?int $limit = null,
        ?int $offset = null
    ) {
        $this->merchantId = $merchantId;
        $this->dateMin = $dateMin;
        $this->dateMax = $dateMax;
        $this->statuses = $statuses;
        $this->query = $query;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @return array<string,mixed>
     */
    public function toQueryParams(): array
    {
        $params = [];

        if ($this->dateMin !== null) {
            $params['date_min'] = $this->dateMin->format('Y-m-d');
        }
        if ($this->dateMax !== null) {
            $params['date_max'] = $this->dateMax->format('Y-m-d');
        }
        if (!empty($this->statuses)) {
            $params['status'] = array_values($this->statuses);
        }
        if ($this->query !== null) {
            $params['query'] = $this->query;
        }
        if ($this->limit !== null) {
            $params['limit'] = $this->limit;
        }
        if ($this->offset !== null) {
            $params['offset'] = $this->offset;
        }

        return $params;
    }
}
