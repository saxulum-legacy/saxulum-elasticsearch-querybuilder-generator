<?php

declare(strict_types=1);

namespace Saxulum\ElasticSearchQueryBuilder\Generator;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
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
     * @param PhpGenerator $phpGenerator
     */
    public function __construct(PhpGenerator $phpGenerator)
    {
        $this->phpGenerator = $phpGenerator;
    }

    /**
     * @param $query
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
        return new New_(new Name('ObjectNode'));
    }

    /**
     * @return Expr
     */
    private function createArrayNode(): Expr
    {
        return new New_(new Name('ArrayNode'));
    }

    /**
     * @param string|float|int|bool|null $value
     * @return Expr
     */
    private function createScalarNode($value): Expr
    {
        if (is_int($value)) {
            return new New_(new Name('IntNode'), [new Arg(new LNumber($value))]);
        } elseif (is_float($value)) {
            return new New_(new Name('FloatNode'), [new Arg(new DNumber($value))]);
        } elseif (is_bool($value)) {
            return new New_(new Name('BoolNode'), [new Arg(new ConstFetch(new Name($value ? 'true' : 'false')))]);
        } elseif (null === $value) {
            return new New_(new Name('StringNode'), [new Arg(new ConstFetch(new Name('null')))]);
        }

        return new New_(new Name('StringNode'), [new Arg(new String_($value))]);
    }

    /**
     * @param array $data
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
     * @param int $position
     * @param array $structuredLines
     */
    private function structuredLine(string $line, string $lastStructuredLine, int &$position, array &$structuredLines)
    {
        if (0 === strpos($line, '->add') &&
            false === strpos($lastStructuredLine, ' )') &&
            false === strpos($lastStructuredLine, 'BoolNode') &&
            false === strpos($lastStructuredLine, 'FloatNode') &&
            false === strpos($lastStructuredLine, 'IntNode') &&
            false === strpos($lastStructuredLine, 'StringNode')) {
            $position++;
        }

        $lineLength = strlen($line);
        $braceCount = 0;

        while (')' === $line[--$lineLength]) {
            $braceCount++;
        }

        $prefix = str_pad('', $position * 4);

        if ($braceCount > 2) {
            $structuredLines[] = $prefix . substr($line, 0, - ($braceCount - 2));
        } else {
            $structuredLines[] = $prefix . $line;
        }

        while ($braceCount-- > 2) {
            $position--;
            $structuredLines[] = str_pad('', $position * 4) . ')';
        }
    }
}
