<?php

namespace Amazon\Entity;

use Amazon\Repository\RestockItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestockItemRepository::class)]
#[ORM\Table(name: "restock_items")]
class RestockItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 55, nullable: true)]
    private ?string $sku = null;

    #[ORM\Column(nullable: true)]
    private ?int $my_soh = null;

    #[ORM\Column(nullable: true)]
    private ?int $total_sales_last_30_days = null;

    #[ORM\Column(nullable: true)]
    private ?int $total_stock_days_cover = null;

    #[ORM\Column(nullable: true)]
    private ?int $items_to_send = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): static
    {
        $this->sku = $sku;
        return $this;
    }

    public function getMySoh(): ?int
    {
        return $this->my_soh;
    }

    public function setMySoh(?int $my_soh): static
    {
        $this->my_soh = $my_soh;
        return $this;
    }

    public function getTotalSalesLast30Days(): ?int
    {
        return $this->total_sales_last_30_days;
    }

    public function setTotalSalesLast30Days(?int $total_sales_last_30_days): static
    {
        $this->total_sales_last_30_days = $total_sales_last_30_days;
        return $this;
    }

    public function getTotalStockDaysCover(): ?int
    {
        return $this->total_stock_days_cover;
    }

    public function setTotalStockDaysCover(?int $total_stock_days_cover): static
    {
        $this->total_stock_days_cover = $total_stock_days_cover;
        return $this;
    }

    public function getItemsToSend(): ?int
    {
        return $this->items_to_send;
    }

    public function setItemsToSend(?int $items_to_send): static
    {
        $this->items_to_send = $items_to_send;
        return $this;
    }
}
