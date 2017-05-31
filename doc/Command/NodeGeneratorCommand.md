# Node Generator Command

```sh
bin/console saxulum:elasticsearch:querybuilder:generator:node '{
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
            "boost": 1.1
        }
    }
}'
```

## Output

```php
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
            ->add('boost', FloatNode::create(1.1))
        )
    );

```

## Run with --useQueryBuilderFactory

```sh
bin/console saxulum:elasticsearch:querybuilder:generator:node --useQueryBuilderFactory '{
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
            "boost": 1.1
        }
    }
}'
```

## Output

```php
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
            ->add('boost', $qb->floatNode(1.1))
        )
    );

```
