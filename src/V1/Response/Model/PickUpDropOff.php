<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use DateTimeImmutable;
use PuntoPost\Sdk\Utils\Date;
use PuntoPost\Sdk\Utils\Getter;

class PickUpDropOff
{
    private string $id;
    private string $externalId;
    private string $type;
    private string $name;
    private string $description;
    private Address $address;
    private string $schedule;
    /** @var ScheduleItem[] */
    private array $scheduleItems;
    private string $phone;
    private bool $enabled;
    private DateTimeImmutable $createdAt;

    /**
     * @param ScheduleItem[] $scheduleItems
     */
    public function __construct(
        string $id,
        string $externalId,
        string $type,
        string $name,
        string $description,
        Address $address,
        string $schedule,
        array $scheduleItems,
        string $phone,
        bool $enabled,
        DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->externalId = $externalId;
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->address = $address;
        $this->schedule = $schedule;
        $this->scheduleItems = $scheduleItems;
        $this->phone = $phone;
        $this->enabled = $enabled;
        $this->createdAt = $createdAt;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $scheduleItemsRaw = Getter::requireArray($data, 'schedule_items', 'PickUpDropOff');
        $scheduleItems = array_map(
            fn ($entry, $index): ScheduleItem => ScheduleItem::fromArray(
                Getter::requireArray($entry, null, sprintf('PickUpDropOff schedule_items[%s]', (string) $index))
            ),
            $scheduleItemsRaw,
            array_keys($scheduleItemsRaw)
        );

        return new self(
            Getter::requireString($data, 'id', 'PickUpDropOff'),
            Getter::requireString($data, 'external_id', 'PickUpDropOff'),
            Getter::requireString($data, 'type', 'PickUpDropOff'),
            Getter::requireString($data, 'name', 'PickUpDropOff'),
            Getter::requireString($data, 'description', 'PickUpDropOff'),
            Address::fromArray(Getter::requireArray($data, 'address', 'PickUpDropOff')),
            Getter::requireString($data, 'schedule', 'PickUpDropOff'),
            $scheduleItems,
            Getter::requireString($data, 'phone', 'PickUpDropOff'),
            Getter::requireBool($data, 'enabled', 'PickUpDropOff'),
            Date::from(Getter::requireString($data, 'created_at', 'PickUpDropOff'))
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    /**
     * @return ScheduleItem[]
     */
    public function getScheduleItems(): array
    {
        return $this->scheduleItems;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
