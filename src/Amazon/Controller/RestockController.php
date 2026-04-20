<?php

namespace Amazon\Controller;

use Amazon\Entity\ManifestFile;
use Amazon\Entity\RestockItem;
use Amazon\Service\ManifestFileGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class RestockController extends AbstractController
{
    #[Route('/restock', name: 'restock_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $restockItems = $entityManager->getRepository(RestockItem::class)->findAll();
        $manifestFiles = $entityManager->getRepository(ManifestFile::class)->findBy([], ['created_at' => 'DESC']);

        return $this->render('restock/index.html.twig', [
            'restockItems' => $restockItems,
            'manifestFiles' => $manifestFiles,
        ]);
    }

    #[Route('/restock/generate', name: 'restock_generate_file', methods: ['POST'])]
    public function generateFile(ManifestFileGenerator $generator, Request $request): Response
    {
        $manifestFile = $generator->generate();

        $this->addFlash('success', 'Manifest file generated: ' . $manifestFile->getFilename());

        return $this->redirectToRoute('restock_index');
    }

    #[Route('/restock/download/{id}', name: 'restock_download_file', methods: ['GET'])]
    public function downloadFile(int $id, EntityManagerInterface $entityManager): Response
    {
        $manifestFile = $entityManager->getRepository(ManifestFile::class)->find($id);

        if (!$manifestFile || !file_exists($manifestFile->getFilepath())) {
            throw $this->createNotFoundException('File not found.');
        }

        $response = new BinaryFileResponse($manifestFile->getFilepath());
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $manifestFile->getFilename()
        );

        return $response;
    }
}
