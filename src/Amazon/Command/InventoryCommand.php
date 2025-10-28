<?php

declare(strict_types=1);

namespace Amazon\Command;

use Amazon\Service\InventorySellingPartnerApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
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
        $this->addOption('delta', null, InputArgument::OPTIONAL);
        $this->addOption('sku', null, InputArgument::OPTIONAL, 'The SKU of the product to fetch inventory for.');
       
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $delta = $input->getOption('delta');
        $sku = $input->getOption('sku');

        if ($delta == 'check') {
            $this->inventoryOrderService->checkDelta();
            return Command::SUCCESS;
        }
        
        if (!empty($sku)) {
            $this->inventoryOrderService->fetchInventoryData($sku);
            return Command::SUCCESS;
        }
        $this->inventoryOrderService->fetchInventoryData();

        return Command::SUCCESS;
    }
}