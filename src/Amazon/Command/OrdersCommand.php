<?php

declare(strict_types=1);

namespace Amazon\Command;

use Amazon\Service\OrdersSellingPartnerApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class OrdersCommand extends Command
{
    /**
     * @var 
     */
    private $amazonOrderService;

    public function __construct(OrdersSellingPartnerApi $amazonOrderService, string $name = null)
    {
        $this->amazonOrderService = $amazonOrderService;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('amazon:fetchorders')->setDescription('Fetches Our Order data from Amazon.');
        $this->addOption('period', null, InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $period = $input->getOption('period');
        $period = empty($period) ? FALSE : $input->getOption('period');
        $this->amazonOrderService->fetchOrderData($period);

        return Command::SUCCESS;
    }
}