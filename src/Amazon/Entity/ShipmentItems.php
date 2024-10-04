<?php

namespace Amazon\Entity;

use Amazon\Repository\ShipmentItemsRepository;
use Doctrine\ORM\Mapping as ORM;
use SellingPartnerApi\Seller\FBAInboundV20240320\Responses\Shipment;

#[ORM\Entity(repositoryClass: ShipmentItemsRepository::class)]
class ShipmentItems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $sellerSku = null;

    #[ORM\Column]
    private ?int $quantityShipped = null;

    #[ORM\Column(length: 50)]
    private ?string $shipmentId = null;

    #[ORM\Column]
    private ?int $quantityReceived = null;

    #[ORM\Column]
    private ?int $quantityInCase = null;

    #[ORM\ManyToOne(targetEntity: Shipments::class, inversedBy: "items")]
    #[ORM\JoinColumn(nullable: false, name: "shipment_id", referencedColumnName: "id")]  
    private $shipment;

    public function getShipment(): ?Shipments
    {
        return $this->shipment;
    }

    public function setShipment(?Shipments $shipment): self
    {
        $this->shipment = $shipment;

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

    public function getSellerSku(): ?string
    {
        return $this->sellerSku;
    }

    public function setSellerSku(string $sellerSku): static
    {
        $this->sellerSku = $sellerSku;

        return $this;
    }

    public function getQuantityShipped(): ?int
    {
        return $this->quantityShipped;
    }

    public function setQuantityShipped(int $quantityShipped): static
    {
        $this->quantityShipped = $quantityShipped;

        return $this;
    }

    public function getShipmentId(): ?string
    {
        return $this->shipmentId;
    }

    public function setShipmentId(string $shipmentId): static
    {
        $this->shipmentId =  $shipmentId;

        return $this;
    }

    public function getQuantityReceived(): ?int
    {
        return $this->quantityReceived;
    }

    public function setQuantityReceived(int $quantityReceived): static
    {
        $this->quantityReceived = $quantityReceived;

        return $this;
    }

    public function getQuantityInCase(): ?int
    {
        return $this->quantityInCase;
    }

    public function setQuantityInCase(int $quantityInCase): static
    {
        $this->quantityInCase = $quantityInCase;

        return $this;
    }
}
