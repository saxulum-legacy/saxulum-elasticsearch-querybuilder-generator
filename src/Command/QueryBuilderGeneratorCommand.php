<?php

declare(strict_types=1);

namespace Saxulum\ElasticSearchQueryBuilder\Generator\Command;

use PhpParser\PrettyPrinter\Standard as PhpGenerator;
use Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @deprecated use Saxulum\ElasticSearchQueryBuilder\Generator\Command\NodeGeneratorCommand
 */
final class QueryBuilderGeneratorCommand extends Command
{
    /**
     * @param string|null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        @trigger_error(sprintf('Use "%s" instead of the "%s"', NodeGeneratorCommand::class, self::class), E_USER_DEPRECATED);
    }

    protected function configure()
    {
        $this
            ->setName('saxulum:elasticsearch:querybuilder:generator:querybuilder')
            ->setDescription('Generate the node code of a elasticsearch json query.')
            ->addArgument('query', InputArgument::REQUIRED, 'The json query.')
            ->addOption('useMethodName', 'm', InputOption::VALUE_NONE, 'Use method names as addToObjectNode')
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
        $generator = new QueryBuilderGenerator(new PhpGenerator(), $input->getOption('useMethodName'));

        $output->writeln('<info>Generated code:</info>');
        $output->writeln($generator->generateByJson($input->getArgument('query')));

        return 0;
    }
}
