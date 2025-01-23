<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use DateTimeInterface;
use DateTime;

abstract class AbstractEntity
{

    #[ORM\Column(type: 'uuid', nullable: false)]
    protected ?Uuid $sid;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected DateTimeInterface $created;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected DateTimeInterface $updated;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTimeInterface $deleted = null;

    public function __construct()
    {
        $this->sid = Uuid::v4();
        $this->created = new DateTime();
        $this->updated = new DateTime();

    }

    public function getSid(): Uuid
    {
        return $this->sid;
    }

    public function setSid(Uuid $sid): static
    {
        $this->sid = $sid;

        return $this;
    }

    public function getCreated(): ?DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(DateTimeInterface $updated): static
    {
        $this->updated = $updated;

        return $this;
    }

    public function getDeleted(): ?DateTimeInterface
    {
        return $this->deleted;
    }

    public function setDeleted(DateTimeInterface $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }
}