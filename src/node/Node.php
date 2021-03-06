<?php declare(strict_types=1);
/*
 * This file is part of sebastian/object-graph.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\ObjectGraph;

use function count;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

final class Node
{
    private int $id;

    private string $className;

    private array $attributes;

    private ?NodeReferenceCollection $referencedNodes = null;

    public function __construct(int $id, string $className, array $attributes)
    {
        $this->id         = $id;
        $this->className  = $className;
        $this->attributes = $attributes;
    }

    public function attributes(): array
    {
        return $this->attributes;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function referencedNodes(): NodeReferenceCollection
    {
        if ($this->referencedNodes !== null) {
            return $this->referencedNodes;
        }

        $referencedNodes = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(
                $this->attributes,
                RecursiveArrayIterator::CHILD_ARRAYS_ONLY
            )
        );

        foreach ($iterator as $element) {
            if ($element instanceof NodeReference) {
                $referencedNodes[] = $element;
            }
        }

        $this->referencedNodes = new NodeReferenceCollection(...$referencedNodes);

        return $this->referencedNodes;
    }

    public function referencesNodes(): bool
    {
        return count($this->referencedNodes()) > 0;
    }
}
