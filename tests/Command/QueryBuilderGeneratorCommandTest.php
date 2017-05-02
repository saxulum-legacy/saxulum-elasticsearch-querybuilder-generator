<?php

namespace Saxulum\Tests\ElasticSearchQueryBuilder\Generator\Command;
use Saxulum\ElasticSearchQueryBuilder\Generator\Command\QueryBuilderGeneratorCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \Saxulum\ElasticSearchQueryBuilder\Generator\Command\QueryBuilderGeneratorCommand
 */
class QueryBuilderGeneratorCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $expect = <<<'EOD'
Generated code:
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('bool', $qb->objectNode())
            ->add('must', $qb->objectNode())
                ->add('term', $qb->objectNode())
                    ->add('user', $qb->stringNode('kimchy'))
                ->end()
            ->end()
            ->add('filter', $qb->objectNode())
                ->add('term', $qb->objectNode())
                    ->add('tag', $qb->stringNode('tech'))
                ->end()
            ->end()
            ->add('must_not', $qb->objectNode())
                ->add('range', $qb->objectNode())
                    ->add('age', $qb->objectNode())
                        ->add('from', $qb->intNode(10))
                        ->add('to', $qb->intNode(20))
                    ->end()
                ->end()
            ->end()
            ->add('should', $qb->arrayNode())
                ->add($qb->objectNode())
                    ->add('term', $qb->objectNode())
                        ->add('tag', $qb->stringNode('wow'))
                    ->end()
                ->end()
                ->add($qb->objectNode())
                    ->add('term', $qb->objectNode())
                        ->add('tag', $qb->stringNode('elasticsearch'))
                    ->end()
                ->end()
            ->end()
            ->add('minimum_should_match', $qb->intNode(1))
            ->add('boost', $qb->floatNode(1.2))
            ->add('enabled', $qb->boolNode(true))
            ->add('relation', $qb->nullNode())
            ->add('array', $qb->arrayNode())
                ->add($qb->arrayNode())
                    ->add($qb->objectNode())
                        ->add('term', $qb->objectNode())
                            ->add('tag', $qb->stringNode('wow'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
    ->end();

EOD;

$json = <<<EOD
{
    "query": {
        "bool": {
            "must": {
                "term": {
                    "user": "kimchy"
                }
            },
            "filter": {
                "term": {
                    "tag": "tech"
                }
            },
            "must_not": {
                "range": {
                    "age": {
                        "from": 10,
                        "to": 20
                    }
                }
            },
            "should": [
                {
                    "term": {
                        "tag": "wow"
                    }
                },
                {
                    "term": {
                        "tag": "elasticsearch"
                    }
                }
            ],
            "minimum_should_match": 1,
            "boost": 1.2,
            "enabled": true,
            "relation": null,
            "array": [
                [
                    {
                        "term": {
                            "tag": "wow"
                        }
                    }
                ]
            ]
        }
    }
}
EOD;

        $input = new ArrayInput(['query' => $json]);
        $output = new BufferedOutput();

        $command = new QueryBuilderGeneratorCommand();
        $command->run($input, $output);

        self::assertSame($expect, $output->fetch());
    }

    public function testExecuteWithUseMethodName()
    {
        $expect = <<<'EOD'
Generated code:
$qb = new QueryBuilder();
$qb
    ->addToObjectNode('query', $qb->objectNode())
        ->addToObjectNode('bool', $qb->objectNode())
            ->addToObjectNode('must', $qb->objectNode())
                ->addToObjectNode('term', $qb->objectNode())
                    ->addToObjectNode('user', $qb->stringNode('kimchy'))
                ->end()
            ->end()
            ->addToObjectNode('filter', $qb->objectNode())
                ->addToObjectNode('term', $qb->objectNode())
                    ->addToObjectNode('tag', $qb->stringNode('tech'))
                ->end()
            ->end()
            ->addToObjectNode('must_not', $qb->objectNode())
                ->addToObjectNode('range', $qb->objectNode())
                    ->addToObjectNode('age', $qb->objectNode())
                        ->addToObjectNode('from', $qb->intNode(10))
                        ->addToObjectNode('to', $qb->intNode(20))
                    ->end()
                ->end()
            ->end()
            ->addToObjectNode('should', $qb->arrayNode())
                ->addToArrayNode($qb->objectNode())
                    ->addToObjectNode('term', $qb->objectNode())
                        ->addToObjectNode('tag', $qb->stringNode('wow'))
                    ->end()
                ->end()
                ->addToArrayNode($qb->objectNode())
                    ->addToObjectNode('term', $qb->objectNode())
                        ->addToObjectNode('tag', $qb->stringNode('elasticsearch'))
                    ->end()
                ->end()
            ->end()
            ->addToObjectNode('minimum_should_match', $qb->intNode(1))
            ->addToObjectNode('boost', $qb->floatNode(1.2))
            ->addToObjectNode('enabled', $qb->boolNode(true))
            ->addToObjectNode('relation', $qb->nullNode())
            ->addToObjectNode('array', $qb->arrayNode())
                ->addToArrayNode($qb->arrayNode())
                    ->addToArrayNode($qb->objectNode())
                        ->addToObjectNode('term', $qb->objectNode())
                            ->addToObjectNode('tag', $qb->stringNode('wow'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
    ->end();

EOD;

$json = <<<EOD
{
    "query": {
        "bool": {
            "must": {
                "term": {
                    "user": "kimchy"
                }
            },
            "filter": {
                "term": {
                    "tag": "tech"
                }
            },
            "must_not": {
                "range": {
                    "age": {
                        "from": 10,
                        "to": 20
                    }
                }
            },
            "should": [
                {
                    "term": {
                        "tag": "wow"
                    }
                },
                {
                    "term": {
                        "tag": "elasticsearch"
                    }
                }
            ],
            "minimum_should_match": 1,
            "boost": 1.2,
            "enabled": true,
            "relation": null,
            "array": [
                [
                    {
                        "term": {
                            "tag": "wow"
                        }
                    }
                ]
            ]
        }
    }
}
EOD;

        $input = new ArrayInput(['query' => $json, '--useMethodName' => true]);
        $output = new BufferedOutput();

        $command = new QueryBuilderGeneratorCommand();
        $command->run($input, $output);

        self::assertSame($expect, $output->fetch());
    }
}
