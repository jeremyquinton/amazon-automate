<?php

declare(strict_types=1);

namespace Amazon\Command;

use Amazon\Service\InventorySellingPartnerApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class InventoryCommand extends Command
{
    /**
     * @var 
     */
    private $inventoryOrderService;

    public function __construct(InventorySellingPartnerApi $inventoryOrderService, string $name = null)
    {
        $this->inventoryOrderService = $inventoryOrderService;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('amazon:fetchinventory')->setDescription('Fetches Our Inventory or Stock Level Info From Amazon.');
        //$this->addOption('period', null, InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
       
        $this->inventoryOrderService->fetchInventoryData();

        return Command::SUCCESS;
    }
}