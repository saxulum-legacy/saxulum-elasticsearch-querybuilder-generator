<?php

namespace Saxulum\Tests\ElasticSearchQueryBuilder\Generator;

use PhpParser\PrettyPrinter\Standard as PhpGenerator;
use PHPUnit\Framework\TestCase;
use Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator;

/**
 * @covers \Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator
 */
class QueryBuilderGeneratorTest extends TestCase
{
    public function testMatchAll()
    {
        $expect = <<<'EOD'
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('match_all', $qb->objectNode())->end()
    ->end();
EOD;

        $json = '{"query":{"match_all":{}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testMatch()
    {
        $expect = <<<'EOD'
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('match', $qb->objectNode())
            ->add('title', $qb->stringNode('elasticsearch'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"match":{"title":"elasticsearch"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testRange()
    {
        $expect = <<<'EOD'
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('range', $qb->objectNode())
            ->add('elements', $qb->objectNode())
                ->add('gte', $qb->intNode(10))
                ->add('lte', $qb->intNode(20))
            ->end()
        ->end()
    ->end();
EOD;

        $json = '{"query":{"range":{"elements":{"gte":10,"lte":20}}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testExists()
    {
        $expect = <<<'EOD'
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('exists', $qb->objectNode())
            ->add('field', $qb->stringNode('text'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"exists":{"field":"text"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testNotExists()
    {
        $expect = <<<'EOD'
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('bool', $qb->objectNode())
            ->add('must_not', $qb->objectNode())
                ->add('exists', $qb->objectNode())
                    ->add('field', $qb->stringNode('text'))
                ->end()
            ->end()
        ->end()
    ->end();
EOD;

        $json = '{"query":{"bool":{"must_not":{"exists":{"field":"text"}}}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testPrefix()
    {
        $expect = <<<'EOD'
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('prefix', $qb->objectNode())
            ->add('title', $qb->stringNode('elastic'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"prefix":{"title":"elastic"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testWildcard()
    {
        $expect = <<<'EOD'
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('wildcard', $qb->objectNode())
            ->add('title', $qb->stringNode('ela*c'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"wildcard":{"title":"ela*c"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testRegexp()
    {
        $expect = <<<'EOD'
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('regexp', $qb->objectNode())
            ->add('title', $qb->stringNode('search$'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"regexp":{"title":"search$"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testFuzzy()
    {
        $expect = <<<'EOD'
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('fuzzy', $qb->objectNode())
            ->add('title', $qb->objectNode())
                ->add('value', $qb->stringNode('sea'))
                ->add('fuzziness', $qb->intNode(2))
            ->end()
        ->end()
    ->end();
EOD;

        $json = '{"query":{"fuzzy":{"title":{"value":"sea","fuzziness":2}}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testType()
    {
        $expect = <<<'EOD'
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('type', $qb->objectNode())
            ->add('value', $qb->stringNode('product'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"type":{"value":"product"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testIds()
    {
        $expect = <<<'EOD'
$qb = new QueryBuilder();
$qb
    ->add('query', $qb->objectNode())
        ->add('ids', $qb->objectNode())
            ->add('type', $qb->stringNode('product'))
            ->add('values', $qb->arrayNode())
                ->add($qb->intNode(1))
                ->add($qb->intNode(2))
            ->end()
        ->end()
    ->end();
EOD;

        $json = '{"query":{"ids":{"type":"product","values":[1,2]}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testComplex()
    {
        $expect = <<<'EOD'
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

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testComplexWithMethodNames()
    {
        $expect = <<<'EOD'
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

        $generator = new QueryBuilderGenerator(new PhpGenerator(), true);

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testWithInvalidJson()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Message: Syntax error, query: {"query":{"ids":{"type":"product","values":[1,2]}}');

        $json = '{"query":{"ids":{"type":"product","values":[1,2]}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Saxulum\ElasticSearchQueryBuilder\Generator\NodeGenerator" instead of the "Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator"', $error['message']);

        $generator->generateByJson($json);
    }
}
