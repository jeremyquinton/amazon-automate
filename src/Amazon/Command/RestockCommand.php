<?php

declare(strict_types=1);

namespace Amazon\Command;

use Amazon\Service\Restock;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class RestockCommand extends Command
{
    /**
     * @var 
     */
    private $restockService;

    public function __construct(Restock $restockService, string $name = null)
    {
        $this->restockService = $restockService;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('amazon:restock')->setDescription('Creates a restock based on sales');
        //$this->addOption('period', null, InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //$this->restockService->play();
        $this->restockService->run();

        return Command::SUCCESS;
    }
}