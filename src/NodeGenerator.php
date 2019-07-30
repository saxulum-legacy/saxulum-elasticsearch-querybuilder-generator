<?php

declare(strict_types=1);

namespace Saxulum\ElasticSearchQueryBuilder\Generator;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\PrettyPrinter\Standard as PhpGenerator;

final class NodeGenerator
{
    /**
     * @var PhpGenerator
     */
    private $phpGenerator;

    /**
     * @var bool
     */
    private $useQueryBuilderFactory;

    /**
     * @param PhpGenerator $phpGenerator
     * @param bool         $useQueryBuilderFactory
     */
    public function __construct(PhpGenerator $phpGenerator, bool $useQueryBuilderFactory = false)
    {
        $this->phpGenerator = $phpGenerator;
        $this->useQueryBuilderFactory = $useQueryBuilderFactory;

        if ($useQueryBuilderFactory) {
            @trigger_error('Argument $useQueryBuilderFactory will be removed', E_USER_DEPRECATED);
        }
    }

    /**
     * @param $query
     *
     * @return string
     */
    public function generateByJson($query): string
    {
        $data = json_decode($query, false);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(sprintf('Message: %s, query: %s', json_last_error_msg(), $query));
        }

        if ($data instanceof \stdClass) {
            $expr = $this->appendChildrenToObjectNode($data);
        } else {
            $expr = $this->appendChildrenToArrayNode($data);
        }

        $code = $this->phpGenerator->prettyPrint([new Assign(new Variable('node'), $expr)]);

        return $this->structureCode($code);
    }

    /**
     * @return Expr
     */
    private function createObjectNode(): Expr
    {
        if (!$this->useQueryBuilderFactory) {
            return new StaticCall(new Name('ObjectNode'), 'create');
        }

        return new MethodCall(new Variable('qb'), 'objectNode');
    }

    /**
     * @return Expr
     */
    private function createArrayNode(): Expr
    {
        if (!$this->useQueryBuilderFactory) {
            return new StaticCall(new Name('ArrayNode'), 'create');
        }

        return new MethodCall(new Variable('qb'), 'arrayNode');
    }

    /**
     * @param string|float|int|bool|null $value
     *
     * @return Expr
     */
    private function createScalarNode($value): Expr
    {
        if (!$this->useQueryBuilderFactory) {
            return $this->createScalarNodeDefault($value);
        }

        return $this->createScalarNodeQueryBuilderFactory($value);
    }

    /**
     * @param string|float|int|bool|null $value
     *
     * @return Expr
     */
    private function createScalarNodeDefault($value): Expr
    {
        if (is_int($value)) {
            return new StaticCall(new Name('IntNode'), 'create', [new Arg(new LNumber($value))]);
        } elseif (is_float($value)) {
            return new StaticCall(new Name('FloatNode'), 'create', [new Arg(new DNumber($value))]);
        } elseif (is_bool($value)) {
            return new StaticCall(new Name('BoolNode'), 'create', [new Arg(new ConstFetch(new Name($value ? 'true' : 'false')))]);
        } elseif (null === $value) {
            return new StaticCall(new Name('NullNode'), 'create');
        }

        return new StaticCall(new Name('StringNode'), 'create', [new Arg(new String_($value))]);
    }

    /**
     * @param string|float|int|bool|null $value
     *
     * @return Expr
     */
    private function createScalarNodeQueryBuilderFactory($value): Expr
    {
        if (is_int($value)) {
            return new MethodCall(new Variable('qb'), 'intNode', [new Arg(new LNumber($value))]);
        } elseif (is_float($value)) {
            return new MethodCall(new Variable('qb'), 'floatNode', [new Arg(new DNumber($value))]);
        } elseif (is_bool($value)) {
            return new MethodCall(new Variable('qb'), 'boolNode', [new Arg(new ConstFetch(new Name($value ? 'true' : 'false')))]);
        } elseif (null === $value) {
            return new MethodCall(new Variable('qb'), 'nullNode');
        }

        return new MethodCall(new Variable('qb'), 'stringNode', [new Arg(new String_($value))]);
    }

    /**
     * @param array $data
     *
     * @return Expr
     */
    private function appendChildrenToArrayNode(array $data)
    {
        $expr = $this->createArrayNode();

        foreach ($data as $key => $value) {
            if ($value instanceof \stdClass) {
                $nodeExpr = $this->createObjectNode();
            } elseif (is_array($value)) {
                $nodeExpr = $this->createArrayNode();
            } else {
                $nodeExpr = $this->createScalarNode($value);
            }

            if ($value instanceof \stdClass) {
                $nodeExpr = $this->appendChildrenToObjectNode($value);
            } elseif (is_array($value)) {
                $nodeExpr = $this->appendChildrenToArrayNode($value);
            }

            $expr = new MethodCall($expr, 'add', [new Arg($nodeExpr)]);
        }

        return $expr;
    }

    /**
     * @param \stdClass $data
     *
     * @return Expr
     */
    private function appendChildrenToObjectNode(\stdClass $data)
    {
        $expr = $this->createObjectNode();

        foreach ($data as $key => $value) {
            if ($value instanceof \stdClass) {
                $nodeExpr = $this->createObjectNode();
            } elseif (is_array($value)) {
                $nodeExpr = $this->createArrayNode();
            } else {
                $nodeExpr = $this->createScalarNode($value);
            }

            if ($value instanceof \stdClass) {
                $nodeExpr = $this->appendChildrenToObjectNode($value);
            } elseif (is_array($value)) {
                $nodeExpr = $this->appendChildrenToArrayNode($value);
            }

            $expr = new MethodCall($expr, 'add', [new Arg(new String_($key)), new Arg($nodeExpr)]);
        }

        return $expr;
    }

    /**
     * @param string $code
     *
     * @return string
     */
    private function structureCode(string $code): string
    {
        $lines = $this->getLinesByCode($code);

        $position = 0;

        $structuredLines = [];

        foreach ($lines as $i => $line) {
            $lastStructuredLine = $structuredLines[count($structuredLines) - 1] ?? '';
            $this->structuredLine($line, $lastStructuredLine, $position, $structuredLines);
        }

        $structuredLines[count($structuredLines) - 1] .= ';';

        return implode("\n", $structuredLines);
    }

    /**
     * @param string $code
     *
     * @return array
     */
    private function getLinesByCode(string $code): array
    {
        $codeWithLinebreaks = str_replace('->add', "\n->add", substr($code, 0, -1));

        return explode("\n", $codeWithLinebreaks);
    }

    /**
     * @param string $line
     * @param string $lastStructuredLine
     * @param int    $position
     * @param array  $structuredLines
     */
    private function structuredLine(string $line, string $lastStructuredLine, int &$position, array &$structuredLines)
    {
        if (0 === strpos($line, '->add') &&
            false === strpos($lastStructuredLine, ' )') &&
            false === strpos($lastStructuredLine, 'oolNode') &&
            false === strpos($lastStructuredLine, 'loatNode') &&
            false === strpos($lastStructuredLine, 'ntNode') &&
            false === strpos($lastStructuredLine, 'ullNode') &&
            false === strpos($lastStructuredLine, 'tringNode')) {
            ++$position;
        }

        $lineLength = strlen($line);
        $braceCount = 0;

        while (')' === $line[--$lineLength]) {
            ++$braceCount;
        }

        $prefix = str_pad('', $position * 4);

        if ($braceCount > 2) {
            $structuredLines[] = $prefix.substr($line, 0, -($braceCount - 2));
        } else {
            $structuredLines[] = $prefix.$line;
        }

        while ($braceCount-- > 2) {
            --$position;
            $structuredLines[] = str_pad('', $position * 4).')';
        }
    }
}
