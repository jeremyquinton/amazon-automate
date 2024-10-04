<?php

namespace Amazon\Command;

use Amazon\Service\ListingSellingPartnerApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'amazon:fetchreport',
    description: 'Fetches a Report from Amazon. The report at the moment is just all our listings from Amazon',
)]
class ListingCommand extends Command
{

    private $listingService;

    public function __construct(ListingSellingPartnerApi $listingService, string $name = null)
    {
        $this->listingService= $listingService;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument(
                'report', 
                InputArgument::REQUIRED, 
                'Description of the required argument' 
            )
            ->addOption(
                'action', 
                null, 
                InputOption::VALUE_REQUIRED, 
                'Description of the required option'
            )
            ->addOption(
                'reportId', 
                null, 
                InputOption::VALUE_OPTIONAL, 
                'The Report Id'
            )
            ->addOption(
                'documentId', 
                null, 
                InputOption::VALUE_OPTIONAL, 
                'The Report Id'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reportType = $input->getArgument('report', 'listings');
        $action = $input->getOption('action', 'generate');
       
        //php bin/console amazon:fetchreport listings --action generate
        if ($reportType == 'listings' && $action == 'generate') {
            $reportId = $this->listingService->generateReport($reportType, $action);
            $output->writeln('Use Command php bin/console amazon:fetchreport listings --action fetch --reportId ' . $reportId . " to fetch this report");    
        }

        if ($reportType == 'listings' && $action == 'fetch') {
            $reportId = $input->getOption('reportId', false); 
            if (!$reportId) {
                $output->writeln('No Report Id specified. Try one of the following');
                $reports = $this->listingService->getReports();
                foreach($reports as $report) {
                   $output->writeln('Report ID : ' . $report->getAmazonReportId());
                   $output->writeln('Use Command php bin/console amazon:fetchreport listings --action fetch --reportId ' . $report->getAmazonReportId());

                }

                return Command::FAILURE;
            }

            $documentId = $this->listingService->fetchReport($reportType, $action, $reportId);
            if ($documentId) {
                $output->writeln('Use Command php bin/console amazon:fetchreport listings --action download --reportId ' . $reportId);
                return Command::SUCCESS;
            }

            $output->writeln('Report is still being generated. Use Command php bin/console amazon:fetchreport listings --action fetch --reportId ' . $documentId );
            return Command::FAILURE;
           
        }

        if ($reportType == 'listings' && $action == 'download') {
            $reportId = $input->getOption('reportId', false); 
            if (!$reportId) {
                $output->writeln('No Document Id specified. Try one of the following');
                $reports = $this->listingService->getLast5CreatedReports();
                foreach ($reports as $report) {
                   $output->writeln('Report ID : ' . $report->getAmazonReportId());
                   $output->writeln('Use Command php bin/console amazon:fetchreport listings --action fetch --reportId ' . $report->getAmazonReportId());

                }
                return Command::FAILURE;
            }

            $this->listingService->downloadReport($reportId);

        }
        
        return Command::SUCCESS;
    }
}
