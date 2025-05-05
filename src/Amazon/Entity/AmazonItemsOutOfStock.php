<?php

namespace Amazon\Entity;

use Amazon\Repository\AmazonItemsOutOfStockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AmazonItemsOutOfStockRepository::class)]
#[ORM\Table(name: "amazonItemsOutOfStock")]
class AmazonItemsOutOfStock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "sellerSku", type: "string", length: 50)]
    private ?string $sellerSku = null;

    #[ORM\Column(name: "productName", type: "string", length: 300)]
    private ?string $productName = null;

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

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }
}
