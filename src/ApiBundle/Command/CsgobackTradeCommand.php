<?php

namespace ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CsgobackTradeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('csgoback:trade')
            ->setDescription('...')
            ->addOption('min_cost', null, InputOption::VALUE_REQUIRED, 'min cost')
            ->addOption('max_cost', null, InputOption::VALUE_REQUIRED, 'max cost')
            ->addOption('percent', null, InputOption::VALUE_REQUIRED, 'percent')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $min_cost = $input->getOption('min_cost');
            $max_cost = $input->getOption('max_cost');
            $percent = $input->getOption('percent');

            $ops_helper = $this->getContainer()->get("api.ops.helper");
            $i = 0;
            while (true) {
                $start_time = time();
                $result = $ops_helper->getTableFromCsGoBack();
                foreach ($result['result'] as $value) {
                    $csgoback_result = $ops_helper->equalPriceCsGoBack($value, $max_cost, $min_cost, $percent);
                    print_r($csgoback_result);
                    if ($csgoback_result != null) {
                        $item = $ops_helper->searchItem($csgoback_result['ops.cost'], 5, $csgoback_result['name'], "730_2");
                        $output->writeln("==========================================\n");
                        $output->writeln("item find \n");
                        $output->writeln("==========================================\n");
                        if (!empty($item['response']['sales'])) {
                            $output->writeln("--------------------------------------\n");
                            $output->writeln($ops_helper->opsByeItem($item['response']));
                            $output->writeln("--------------------------------------\n");
                            $max_cost = $ops_helper->getBalance();
                            sleep(1);
                        } else {
                            $output->writeln("--------------------------------------\n");
                            $output->writeln('SO SLOW');
                            $output->writeln("--------------------------------------\n");
                        }
                    }
                }
                print_r((time() - $start_time) / 60 . "\n");
                print_r(count($result['result']) . "\n");
                $i++;
                if ($i == 10) {
                    $max_cost = $ops_helper->getBalance();
                    $i = 0;
                }
                print_r($max_cost . "\n");
                sleep(10);
            }
        } catch (\Exception $e) {

        }
    }

}
