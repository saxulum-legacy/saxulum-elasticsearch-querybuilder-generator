<?php

declare(strict_types=1);

namespace Saxulum\ElasticSearchQueryBuilder\Generator\Command;

use PhpParser\PrettyPrinter\Standard as PhpGenerator;
use Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class NodeGeneratorCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('saxulum:elasticsearch:querybuilder:generator:node')
            ->setDescription('Generate the node code of a elasticsearch json query.')
            ->addArgument('query', InputArgument::REQUIRED, 'The json query.')
            ->addOption('useQueryBuilderFactory', 'f', InputOption::VALUE_NONE, 'Use query builder factory')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generator = new NodeGenerator(new PhpGenerator(), $input->getOption('useQueryBuilderFactory'));

        $output->writeln('<info>Generated code:</info>');
        $output->writeln($generator->generateByJson($input->getArgument('query')));

        return 0;
    }
}
