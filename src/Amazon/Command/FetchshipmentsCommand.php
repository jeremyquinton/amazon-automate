<?php

namespace Amazon\Command;

use Amazon\Service\FetchShipmentsSellingPartnerApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'amazon:fetchshipments',
    description: 'Fetches all our items on the way to Amazon so we dont over stock amazon',
)]
class FetchshipmentsCommand extends Command
{

    private $shipmentService;

    public function __construct(FetchShipmentsSellingPartnerApi $shipmentsService)
    {
        $this->shipmentService = $shipmentsService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->shipmentService->fetchShipmentData();

        return Command::SUCCESS;
    }
}
