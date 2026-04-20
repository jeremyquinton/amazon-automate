<?php

namespace Amazon\Service;

use Amazon\Entity\ManifestFile;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ManifestFileGenerator
{
    private EntityManagerInterface $entityManager;

    private string $templatePath;

    private string $outputDir;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->templatePath = getcwd() . '/ManifestFileUpload_Template_MPL_2.xlsx';
        $this->outputDir = getcwd() . '/restock_files/';
    }

    public function generate(): ManifestFile
    {
        $query = "select sku, items_to_send from restock_items";
        $items = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();

        $spreadsheet = IOFactory::load($this->templatePath);
        $sheet = $spreadsheet->getSheetByName("Create workflow – template");

        $row = 9;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $item['sku']);
            $sheet->setCellValue('B' . $row, $item['items_to_send']);
            $row++;
        }

        $now = new \DateTime();
        $filename = 'ManifestFileUpload_Template_MPL_' . $now->format('Y_m_d_His') . '.xlsx';
        $filepath = $this->outputDir . $filename;

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filepath);

        $manifestFile = new ManifestFile();
        $manifestFile->setFilename($filename);
        $manifestFile->setFilepath($filepath);
        $manifestFile->setCreatedAt($now);
        $manifestFile->setItemCount(count($items));

        $this->entityManager->persist($manifestFile);
        $this->entityManager->flush();

        return $manifestFile;
    }

    public function getOutputDir(): string
    {
        return $this->outputDir;
    }
}
