<?php

namespace Amazon\Entity;

use Amazon\Repository\ShipmentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipmentsRepository::class)]
class Shipments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $amazonShipmentId = null;

    #[ORM\Column(length: 100)]
    private ?string $shipmentName = null;

    #[ORM\Column]
    private ?string $destinationFulfillmentCenterId = null;

    #[ORM\Column(length: 50)]
    private ?string $shipmentStatus = null;

    #[ORM\Column(length: 40)]
    private ?string $labelPrepType = null;

    #[ORM\Column(nullable: true)]
    private ?int $areCasesRequired = null;

    #[ORM\OneToMany(mappedBy: "shipment", targetEntity: ShipmentItems::class, cascade: ["persist", "remove"])]
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return Collection<int, ShipmentItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ShipmentItems $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setShipment($this);
        }

        return $this;
    }

    public function removeItem(ShipmentItems $item): self
    {
        if ($this->items->removeElement($item)) {
            // Set the owning side to null (unless already changed)
            if ($item->getShipment() === $this) {
                $item->setShipment(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getAmazonShipmentId(): ?string
    {
        return $this->amazonShipmentId;
    }

    public function setAmazonShipmentId(string $amazonShipmentId): static
    {
        $this->amazonShipmentId = $amazonShipmentId;

        return $this;
    }

    public function getShipmentName(): ?string
    {
        return $this->shipmentName;
    }

    public function setShipmentName(string $shipmentName): static
    {
        $this->shipmentName = $shipmentName;

        return $this;
    }

    public function getDestinationFulfillmentCenterId(): ?string
    {
        return $this->destinationFulfillmentCenterId;
    }

    public function setDestinationFulfillmentCenterId(string $destinationFulfillmentCenterId): static
    {
        $this->destinationFulfillmentCenterId = $destinationFulfillmentCenterId;

        return $this;
    }

    public function getShipmentStatus(): ?string
    {
        return $this->shipmentStatus;
    }

    public function setShipmentStatus(string $shipmentStatus): static
    {
        $this->shipmentStatus = $shipmentStatus;

        return $this;
    }

    public function getLabelPrepType(): ?string
    {
        return $this->labelPrepType;
    }

    public function setLabelPrepType(string $labelPrepType): static
    {
        $this->labelPrepType = $labelPrepType;

        return $this;
    }

    public function getAreCasesRequired(): ?int
    {
        return $this->areCasesRequired;
    }

    public function setAreCasesRequired(?int $areCasesRequired): static
    {
        $this->areCasesRequired = $areCasesRequired;

        return $this;
    }
}
