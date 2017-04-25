<?php

namespace Saxulum\Tests\ElasticSearchQueryBuilder\Generator;

use PhpParser\PrettyPrinter\Standard as PhpGenerator;
use Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator;

/**
 * @covers \Saxulum\ElasticSearchQueryBuilder\Generator\QueryBuilderGenerator
 */
class QueryBuilderGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchAll()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('match_all', $queryBuilder->objectNode())->end()
    ->end();
EOD;

        $json = '{"query":{"match_all":{}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testMatch()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('match', $queryBuilder->objectNode())
            ->add('title', $queryBuilder->stringNode('elasticsearch'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"match":{"title":"elasticsearch"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testRange()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('range', $queryBuilder->objectNode())
            ->add('elements', $queryBuilder->objectNode())
                ->add('gte', $queryBuilder->intNode(10))
                ->add('lte', $queryBuilder->intNode(20))
            ->end()
        ->end()
    ->end();
EOD;

        $json = '{"query":{"range":{"elements":{"gte":10,"lte":20}}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testExists()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('exists', $queryBuilder->objectNode())
            ->add('field', $queryBuilder->stringNode('text'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"exists":{"field":"text"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testNotExists()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('bool', $queryBuilder->objectNode())
            ->add('must_not', $queryBuilder->objectNode())
                ->add('exists', $queryBuilder->objectNode())
                    ->add('field', $queryBuilder->stringNode('text'))
                ->end()
            ->end()
        ->end()
    ->end();
EOD;

        $json = '{"query":{"bool":{"must_not":{"exists":{"field":"text"}}}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testPrefix()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('prefix', $queryBuilder->objectNode())
            ->add('title', $queryBuilder->stringNode('elastic'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"prefix":{"title":"elastic"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testWildcard()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('wildcard', $queryBuilder->objectNode())
            ->add('title', $queryBuilder->stringNode('ela*c'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"wildcard":{"title":"ela*c"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testRegexp()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('regexp', $queryBuilder->objectNode())
            ->add('title', $queryBuilder->stringNode('search$'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"regexp":{"title":"search$"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testFuzzy()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('fuzzy', $queryBuilder->objectNode())
            ->add('title', $queryBuilder->objectNode())
                ->add('value', $queryBuilder->stringNode('sea'))
                ->add('fuzziness', $queryBuilder->intNode(2))
            ->end()
        ->end()
    ->end();
EOD;

        $json = '{"query":{"fuzzy":{"title":{"value":"sea","fuzziness":2}}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testType()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('type', $queryBuilder->objectNode())
            ->add('value', $queryBuilder->stringNode('product'))
        ->end()
    ->end();
EOD;

        $json = '{"query":{"type":{"value":"product"}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testIds()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('ids', $queryBuilder->objectNode())
            ->add('type', $queryBuilder->stringNode('product'))
            ->add('values', $queryBuilder->arrayNode())
                ->add($queryBuilder->intNode(1))
                ->add($queryBuilder->intNode(2))
            ->end()
        ->end()
    ->end();
EOD;

        $json = '{"query":{"ids":{"type":"product","values":[1,2]}}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testComplex()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->add('query', $queryBuilder->objectNode())
        ->add('bool', $queryBuilder->objectNode())
            ->add('must', $queryBuilder->objectNode())
                ->add('term', $queryBuilder->objectNode())
                    ->add('user', $queryBuilder->stringNode('kimchy'))
                ->end()
            ->end()
            ->add('filter', $queryBuilder->objectNode())
                ->add('term', $queryBuilder->objectNode())
                    ->add('tag', $queryBuilder->stringNode('tech'))
                ->end()
            ->end()
            ->add('must_not', $queryBuilder->objectNode())
                ->add('range', $queryBuilder->objectNode())
                    ->add('age', $queryBuilder->objectNode())
                        ->add('from', $queryBuilder->intNode(10))
                        ->add('to', $queryBuilder->intNode(20))
                    ->end()
                ->end()
            ->end()
            ->add('should', $queryBuilder->arrayNode())
                ->add($queryBuilder->objectNode())
                    ->add('term', $queryBuilder->objectNode())
                        ->add('tag', $queryBuilder->stringNode('wow'))
                    ->end()
                ->end()
                ->add($queryBuilder->objectNode())
                    ->add('term', $queryBuilder->objectNode())
                        ->add('tag', $queryBuilder->stringNode('elasticsearch'))
                    ->end()
                ->end()
            ->end()
            ->add('minimum_should_match', $queryBuilder->intNode(1))
            ->add('boost', $queryBuilder->floatNode(1.2))
            ->add('enabled', $queryBuilder->boolNode(true))
            ->add('relation', $queryBuilder->nullNode())
            ->add('array', $queryBuilder->arrayNode())
                ->add($queryBuilder->arrayNode())
                    ->add($queryBuilder->objectNode())
                        ->add('term', $queryBuilder->objectNode())
                            ->add('tag', $queryBuilder->stringNode('wow'))
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

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testComplexWithMethodNames()
    {
        $expect = <<<'EOD'
$queryBuilder = new QueryBuilder();
$queryBuilder
    ->addToObjectNode('query', $queryBuilder->objectNode())
        ->addToObjectNode('bool', $queryBuilder->objectNode())
            ->addToObjectNode('must', $queryBuilder->objectNode())
                ->addToObjectNode('term', $queryBuilder->objectNode())
                    ->addToObjectNode('user', $queryBuilder->stringNode('kimchy'))
                ->end()
            ->end()
            ->addToObjectNode('filter', $queryBuilder->objectNode())
                ->addToObjectNode('term', $queryBuilder->objectNode())
                    ->addToObjectNode('tag', $queryBuilder->stringNode('tech'))
                ->end()
            ->end()
            ->addToObjectNode('must_not', $queryBuilder->objectNode())
                ->addToObjectNode('range', $queryBuilder->objectNode())
                    ->addToObjectNode('age', $queryBuilder->objectNode())
                        ->addToObjectNode('from', $queryBuilder->intNode(10))
                        ->addToObjectNode('to', $queryBuilder->intNode(20))
                    ->end()
                ->end()
            ->end()
            ->addToObjectNode('should', $queryBuilder->arrayNode())
                ->addToArrayNode($queryBuilder->objectNode())
                    ->addToObjectNode('term', $queryBuilder->objectNode())
                        ->addToObjectNode('tag', $queryBuilder->stringNode('wow'))
                    ->end()
                ->end()
                ->addToArrayNode($queryBuilder->objectNode())
                    ->addToObjectNode('term', $queryBuilder->objectNode())
                        ->addToObjectNode('tag', $queryBuilder->stringNode('elasticsearch'))
                    ->end()
                ->end()
            ->end()
            ->addToObjectNode('minimum_should_match', $queryBuilder->intNode(1))
            ->addToObjectNode('boost', $queryBuilder->floatNode(1.2))
            ->addToObjectNode('enabled', $queryBuilder->boolNode(true))
            ->addToObjectNode('relation', $queryBuilder->nullNode())
            ->addToObjectNode('array', $queryBuilder->arrayNode())
                ->addToArrayNode($queryBuilder->arrayNode())
                    ->addToArrayNode($queryBuilder->objectNode())
                        ->addToObjectNode('term', $queryBuilder->objectNode())
                            ->addToObjectNode('tag', $queryBuilder->stringNode('wow'))
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

        self::assertSame($expect, $generator->generateByJson($json));
    }

    public function testWithInvalidJson()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Message: Syntax error, query: {"query":{"ids":{"type":"product","values":[1,2]}}');

        $json = '{"query":{"ids":{"type":"product","values":[1,2]}}';

        $generator = new QueryBuilderGenerator(new PhpGenerator());
        $generator->generateByJson($json);
    }
}
