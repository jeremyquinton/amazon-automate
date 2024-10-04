<?php

namespace Amazon\Entity;

use Amazon\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_time = null;

    #[ORM\Column(length: 50)]
    private ?string $processing_status = null;

    #[ORM\Column(length: 100)]
    private ?string $marketplace_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $data_start_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $data_end_time = null;

    #[ORM\Column]
    private ?int $report_schedule_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $processing_start_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $processing_end_time = null;

    #[ORM\Column(length: 255)]
    private ?string $report_document_id = null;

    #[ORM\Column(length: 20)]
    private ?int $amazon_report_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCreatedTime(): ?\DateTimeInterface
    {
        return $this->created_time;
    }

    public function setCreatedTime(\DateTimeInterface $created_time): static
    {
        $this->created_time = $created_time;

        return $this;
    }

    public function getProcessingStatus(): ?string
    {
        return $this->processing_status;
    }

    public function setProcessingStatus(string $processing_status): static
    {
        $this->processing_status = $processing_status;

        return $this;
    }

    public function getMarketplaceId(): ?string
    {
        return $this->marketplace_id;
    }

    public function setMarketplaceId(string $marketplace_id): static
    {
        $this->marketplace_id = $marketplace_id;

        return $this;
    }

    public function getDataStartTime(): ?\DateTimeInterface
    {
        return $this->data_start_time;
    }

    public function setDataStartTime(\DateTimeInterface $data_start_time): static
    {
        $this->data_start_time = $data_start_time;

        return $this;
    }

    public function getDataEndTime(): ?\DateTimeInterface
    {
        return $this->data_end_time;
    }

    public function setDataEndTime(\DateTimeInterface $data_end_time): static
    {
        $this->data_end_time = $data_end_time;

        return $this;
    }

    public function getReportScheduleId(): ?int
    {
        return $this->report_schedule_id;
    }

    public function setReportScheduleId(int $report_schedule_id): static
    {
        $this->report_schedule_id = $report_schedule_id;

        return $this;
    }

    public function getProcessingStartTime(): ?\DateTimeInterface
    {
        return $this->processing_start_time;
    }

    public function setProcessingStartTime(\DateTimeInterface $processing_start_time): static
    {
        $this->processing_start_time = $processing_start_time;

        return $this;
    }

    public function getProcessingEndTime(): ?\DateTimeInterface
    {
        return $this->processing_end_time;
    }

    public function setProcessingEndTime(\DateTimeInterface $processing_end_time): static
    {
        $this->processing_end_time = $processing_end_time;

        return $this;
    }

    public function getReportDocumentId(): ?string
    {
        return $this->report_document_id;
    }

    public function setReportDocumentId(string $report_document_id): static
    {
        $this->report_document_id = $report_document_id;

        return $this;
    }

    public function getAmazonReportId(): ?string
    {
        return $this->amazon_report_id;
    }

    public function setAmazonReportId(string $amazon_report_id): static
    {
        $this->amazon_report_id = $amazon_report_id;

        return $this;
    }
}
