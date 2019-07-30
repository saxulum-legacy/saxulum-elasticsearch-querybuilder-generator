<?php

namespace Saxulum\Tests\ElasticSearchQueryBuilder\Generator\Command;

use PHPUnit\Framework\TestCase;
use Saxulum\ElasticSearchQueryBuilder\Generator\Command\NodeGeneratorCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \Saxulum\ElasticSearchQueryBuilder\Generator\Command\NodeGeneratorCommand
 */
class NodeGeneratorCommandTest extends TestCase
{
    public function testExecute()
    {
        $expect = <<<'EOD'
Generated code:
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('bool', ObjectNode::create()
            ->add('must', ObjectNode::create()
                ->add('term', ObjectNode::create()
                    ->add('user', StringNode::create('kimchy'))
                )
            )
            ->add('filter', ObjectNode::create()
                ->add('term', ObjectNode::create()
                    ->add('tag', StringNode::create('tech'))
                )
            )
            ->add('must_not', ObjectNode::create()
                ->add('range', ObjectNode::create()
                    ->add('age', ObjectNode::create()
                        ->add('from', IntNode::create(10))
                        ->add('to', IntNode::create(20))
                    )
                )
            )
            ->add('should', ArrayNode::create()
                ->add(ObjectNode::create()
                    ->add('term', ObjectNode::create()
                        ->add('tag', StringNode::create('wow'))
                    )
                )
                ->add(ObjectNode::create()
                    ->add('term', ObjectNode::create()
                        ->add('tag', StringNode::create('elasticsearch'))
                    )
                )
            )
            ->add('minimum_should_match', IntNode::create(1))
            ->add('boost', FloatNode::create(1.2))
            ->add('enabled', BoolNode::create(true))
            ->add('relation', NullNode::create())
            ->add('array', ArrayNode::create()
                ->add(ArrayNode::create()
                    ->add(ObjectNode::create()
                        ->add('term', ObjectNode::create()
                            ->add('tag', StringNode::create('wow'))
                        )
                    )
                )
            )
        )
    );

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

        $command = new NodeGeneratorCommand();
        $command->run($input, $output);

        self::assertSame($expect, $output->fetch());

        $error = error_get_last();

        self::assertNull($error);
    }

    public function testExecuteWithQueryBuilderFactory()
    {
        $expect = <<<'EOD'
Generated code:
$node = $qb->objectNode()
    ->add('query', $qb->objectNode()
        ->add('bool', $qb->objectNode()
            ->add('must', $qb->objectNode()
                ->add('term', $qb->objectNode()
                    ->add('user', $qb->stringNode('kimchy'))
                )
            )
            ->add('filter', $qb->objectNode()
                ->add('term', $qb->objectNode()
                    ->add('tag', $qb->stringNode('tech'))
                )
            )
            ->add('must_not', $qb->objectNode()
                ->add('range', $qb->objectNode()
                    ->add('age', $qb->objectNode()
                        ->add('from', $qb->intNode(10))
                        ->add('to', $qb->intNode(20))
                    )
                )
            )
            ->add('should', $qb->arrayNode()
                ->add($qb->objectNode()
                    ->add('term', $qb->objectNode()
                        ->add('tag', $qb->stringNode('wow'))
                    )
                )
                ->add($qb->objectNode()
                    ->add('term', $qb->objectNode()
                        ->add('tag', $qb->stringNode('elasticsearch'))
                    )
                )
            )
            ->add('minimum_should_match', $qb->intNode(1))
            ->add('boost', $qb->floatNode(1.2))
            ->add('enabled', $qb->boolNode(true))
            ->add('relation', $qb->nullNode())
            ->add('array', $qb->arrayNode()
                ->add($qb->arrayNode()
                    ->add($qb->objectNode()
                        ->add('term', $qb->objectNode()
                            ->add('tag', $qb->stringNode('wow'))
                        )
                    )
                )
            )
        )
    );

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

        $input = new ArrayInput(['query' => $json, '--useQueryBuilderFactory' => true]);
        $output = new BufferedOutput();

        $command = new NodeGeneratorCommand();
        $command->run($input, $output);

        self::assertSame($expect, $output->fetch());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Argument $useQueryBuilderFactory will be removed', $error['message']);
    }
}
