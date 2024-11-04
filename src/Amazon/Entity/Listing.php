<?php

namespace Amazon\Entity;

use Amazon\Repository\ListingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListingRepository::class)]
class Listing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $item_name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $item_description = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $listing_id = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $seller_sku = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $open_date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_url = null;

    #[ORM\Column(nullable: true)]
    private ?int $item_is_marketplace = null;

    #[ORM\Column(nullable: true)]
    private ?int $product_id_type = null;

    #[ORM\Column(nullable: true)]
    private ?float $zshop_shipping_fee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $item_note = null;

    #[ORM\Column(nullable: true)]
    private ?int $item_condition = null;

    #[ORM\Column(length: 50)]
    private ?string $asin1 = null;

    #[ORM\Column(length: 50)]
    private ?string $asin2 = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $string = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $product_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $bid_for_featured_placement = null;

    #[ORM\Column(nullable: true)]
    private ?int $add_delete = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $pending_quantity = null;

    #[ORM\Column(nullable: true)]
    private ?int $option_payment_type_exclusion = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(length: 50)]
    private ?string $fulfilment_channel = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastUpdatedDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItemName(): ?string
    {
        return $this->item_name;
    }

    public function setItemName(string $item_name): static
    {
        $this->item_name = $item_name;

        return $this;
    }

    public function getItemDescription(): ?string
    {
        return $this->item_description;
    }

    public function setItemDescription(?string $item_description): static
    {
        $this->item_description = $item_description;

        return $this;
    }

    public function getListingId(): ?string
    {
        return $this->listing_id;
    }

    public function setListingId(?string $listing_id): static
    {
        $this->listing_id = $listing_id;

        return $this;
    }

    public function getSellerSku(): ?string
    {
        return $this->seller_sku;
    }

    public function setSellerSku(?string $seller_sku): static
    {
        $this->seller_sku = $seller_sku;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOpenDate(): ?\DateTimeInterface
    {
        return $this->open_date;
    }

    public function setOpenDate(?\DateTimeInterface $open_date): static
    {
        $this->open_date = $open_date;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(?string $image_url): static
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getItemIsMarketplace(): ?int
    {
        return $this->item_is_marketplace;
    }

    public function setItemIsMarketplace(?int $item_is_marketplace): static
    {
        $this->item_is_marketplace = $item_is_marketplace;

        return $this;
    }

    public function getProductIdType(): ?int
    {
        return $this->product_id_type;
    }

    public function setProductIdType(?int $product_id_type): static
    {
        $this->product_id_type = $product_id_type;

        return $this;
    }

    public function getZshopShippingFee(): ?float
    {
        return $this->zshop_shipping_fee;
    }

    public function setZshopShippingFee(?float $zshop_shipping_fee): static
    {
        $this->zshop_shipping_fee = $zshop_shipping_fee;

        return $this;
    }

    public function getItemNote(): ?string
    {
        return $this->item_note;
    }

    public function setItemNote(?string $item_note): static
    {
        $this->item_note = $item_note;

        return $this;
    }

    public function getItemCondition(): ?int
    {
        return $this->item_condition;
    }

    public function setItemCondition(?int $item_condition): static
    {
        $this->item_condition = $item_condition;

        return $this;
    }

    public function getAsin1(): ?string
    {
        return $this->asin1;
    }

    public function setAsin1(string $asin1): static
    {
        $this->asin1 = $asin1;

        return $this;
    }

    public function getAsin2(): ?string
    {
        return $this->asin2;
    }

    public function setAsin2(string $asin2): static
    {
        $this->asin2 = $asin2;

        return $this;
    }

    public function getString(): ?string
    {
        return $this->string;
    }

    public function setString(?string $string): static
    {
        $this->string = $string;

        return $this;
    }

    public function getProductId(): ?string
    {
        return $this->product_id;
    }

    public function setProductId(?string $product_id): static
    {
        $this->product_id = $product_id;

        return $this;
    }

    public function getBidForFeaturedPlacement(): ?int
    {
        return $this->bid_for_featured_placement;
    }

    public function setBidForFeaturedPlacement(?int $bid_for_featured_placement): static
    {
        $this->bid_for_featured_placement = $bid_for_featured_placement;

        return $this;
    }

    public function getAddDelete(): ?int
    {
        return $this->add_delete;
    }

    public function setAddDelete(?int $add_delete): static
    {
        $this->add_delete = $add_delete;

        return $this;
    }

    public function getPendingQuantity(): ?string
    {
        return $this->pending_quantity;
    }

    public function setPendingQuantity(?string $pending_quantity): static
    {
        $this->pending_quantity = $pending_quantity;

        return $this;
    }

    public function getOptionPaymentTypeExclusion(): ?int
    {
        return $this->option_payment_type_exclusion;
    }

    public function setOptionPaymentTypeExclusion(?int $option_payment_type_exclusion): static
    {
        $this->option_payment_type_exclusion = $option_payment_type_exclusion;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getFulfilmentChannel(): ?string
    {
        return $this->fulfilment_channel;
    }

    public function setFulfilmentChannel(string $fulfilment_channel): static
    {
        $this->fulfilment_channel = $fulfilment_channel;

        return $this;
    }

    public function getLastUpdatedDate(): ?\DateTimeInterface
    {
        return $this->lastUpdatedDate;
    }

    public function setLastUpdatedDate(\DateTimeInterface $lastUpdatedDate): static
    {
        $this->lastUpdatedDate = $lastUpdatedDate;

        return $this;
    }
}
