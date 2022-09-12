<?php

namespace Smoren\Testing;

class BreakPieces
{
    public function process(string $shape): array
    {
        $matrix = new Matrix($shape);
        $paths = $this->getPaths($matrix);

        $shapes = array_unique(array_map(function($path) {
            return Matrix::fromCollection($path)->stringify();
        }, $paths));

        $matrices = array_map(function($shape) {
            return new Matrix($shape);
        }, $shapes);

        $contexts = array_map(function($matrix) {
            return new ResultPathContext($this->getPaths($matrix)[0]);
        }, $matrices);

        $contextMap = [];
        foreach($contexts as $context) {
            $hash = $context->hash();
            if(!isset($contextMap[$hash])) {
                $contextMap[$hash] = [];
            }
            $contextMap[$hash][] = $context;
        }

        foreach($contextMap as &$contexts) {
            usort($contexts, function(ResultPathContext $lhs, ResultPathContext $rhs) {
                return $lhs->path->getPerimeter() - $rhs->path->getPerimeter();
            });
        }

        $paths = array_map(function($contexts) {
            return $contexts[0]->path;
        }, $contextMap);

        $result = [];
        foreach($paths as $path) {
            $result[] = Matrix::fromCollection($path->getNormalized())->stringify();
        }

        return $result;
    }

    /**
     * @param Matrix $matrix
     * @return array<VectorCollection>
     */
    protected function getPaths(Matrix $matrix): array
    {
        /** @var VectorCollection[] $paths */
        $paths = [];
        $contexts = [
            new TraverseContext(
                $matrix->getFirstPosition(),
                new Direction(-1, 0),
                new VectorCollection(),
                new VectorCollection()
            )
        ];

        while(count($contexts)) {
            $context = array_pop($contexts);

            if($matrix->getValue($context->position) === '+') {
                if($context->passed->isset($context->position)) {
                    if($context->points->isset($context->position)) {
                        $paths[] = $context->points->getSubCollection($context->position);
                    }
                    continue;
                }

                $possibleDirections = $matrix->getPossibleDirections($context->position, $context->direction);
                foreach($possibleDirections as $direction) {
                    $newContext = clone $context;
                    $newContext->direction = $direction;
                    $newContext->position = $context->position->add($direction);

                    $newContext->passed->add($context->position);
                    if(!$context->direction->equals($direction)) {
                        $newContext->points->add($context->position);
                    }

                    $contexts[] = $newContext;
                }
            } else {
                $context->position = $context->position->add($context->direction);
                $contexts[] = $context;
            }
        }

        return $paths;
    }
}
