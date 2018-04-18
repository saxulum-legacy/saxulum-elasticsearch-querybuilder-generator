<?php

namespace Saxulum\Tests\ElasticSearchQueryBuilder\Generator;

use PhpParser\PrettyPrinter\Standard as PhpGenerator;
use PHPUnit\Framework\TestCase;
use Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator;

/**
 * @covers \Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator
 */
class NodeGeneratorTest extends TestCase
{
    public function testMatchAll()
    {
        $expect = <<<'EOD'
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('match_all', ObjectNode::create())
    );
EOD;

        $json = '{"query":{"match_all":{}}}';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testMatch()
    {
        $expect = <<<'EOD'
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('match', ObjectNode::create()
            ->add('title', StringNode::create('elasticsearch'))
        )
    );
EOD;

        $json = '{"query":{"match":{"title":"elasticsearch"}}}';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testRange()
    {
        $expect = <<<'EOD'
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('range', ObjectNode::create()
            ->add('elements', ObjectNode::create()
                ->add('gte', IntNode::create(10))
                ->add('lte', IntNode::create(20))
            )
        )
    );
EOD;

        $json = '{"query":{"range":{"elements":{"gte":10,"lte":20}}}}';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testExists()
    {
        $expect = <<<'EOD'
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('exists', ObjectNode::create()
            ->add('field', StringNode::create('text'))
        )
    );
EOD;

        $json = '{"query":{"exists":{"field":"text"}}}';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testNotExists()
    {
        $expect = <<<'EOD'
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('bool', ObjectNode::create()
            ->add('must_not', ObjectNode::create()
                ->add('exists', ObjectNode::create()
                    ->add('field', StringNode::create('text'))
                )
            )
        )
    );
EOD;

        $json = '{"query":{"bool":{"must_not":{"exists":{"field":"text"}}}}}';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testPrefix()
    {
        $expect = <<<'EOD'
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('prefix', ObjectNode::create()
            ->add('title', StringNode::create('elastic'))
        )
    );
EOD;

        $json = '{"query":{"prefix":{"title":"elastic"}}}';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testWildcard()
    {
        $expect = <<<'EOD'
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('wildcard', ObjectNode::create()
            ->add('title', StringNode::create('ela*c'))
        )
    );
EOD;

        $json = '{"query":{"wildcard":{"title":"ela*c"}}}';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testRegexp()
    {
        $expect = <<<'EOD'
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('regexp', ObjectNode::create()
            ->add('title', StringNode::create('search$'))
        )
    );
EOD;

        $json = '{"query":{"regexp":{"title":"search$"}}}';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testFuzzy()
    {
        $expect = <<<'EOD'
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('fuzzy', ObjectNode::create()
            ->add('title', ObjectNode::create()
                ->add('value', StringNode::create('sea'))
                ->add('fuzziness', IntNode::create(2))
            )
        )
    );
EOD;

        $json = '{"query":{"fuzzy":{"title":{"value":"sea","fuzziness":2}}}}';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testType()
    {
        $expect = <<<'EOD'
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('type', ObjectNode::create()
            ->add('value', StringNode::create('product'))
        )
    );
EOD;

        $json = '{"query":{"type":{"value":"product"}}}';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testIds()
    {
        $expect = <<<'EOD'
$node = ObjectNode::create()
    ->add('query', ObjectNode::create()
        ->add('ids', ObjectNode::create()
            ->add('type', StringNode::create('product'))
            ->add('values', ArrayNode::create()
                ->add(IntNode::create(1))
                ->add(IntNode::create(2))
            )
        )
    );
EOD;

        $json = '{"query":{"ids":{"type":"product","values":[1,2]}}}';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testWithArrayNode()
    {
        $expect = <<<'EOD'
$node = ArrayNode::create()
    ->add(IntNode::create(1))
    ->add(IntNode::create(2));
EOD;

        $json = '[1,2]';

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testComplex()
    {
        $expect = <<<'EOD'
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

        $generator = new NodeGenerator(new PhpGenerator());

        $error = error_get_last();

        self::assertNull($error);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testComplexWithQueryBuilderFactory()
    {
        $expect = <<<'EOD'
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

        $generator = new NodeGenerator(new PhpGenerator(), true);

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Argument $useQueryBuilderFactory will be removed', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testWithInvalidJson()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Message: Syntax error, query: {"query":{"ids":{"type":"product","values":[1,2]}}');

        $json = '{"query":{"ids":{"type":"product","values":[1,2]}}';

        $generator = new NodeGenerator(new PhpGenerator());
        $generator->generateByJson($json);
    }
}
