<?php

namespace Smoren\Testing;

class BreakPieces
{
    public function process($shape): array
    {
        $matrix = new Matrix($shape);
        $points = new VectorCollection();
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
                $points->add($context->position);

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

        $resultPaths = [];
        foreach($paths as $lhs) {
            foreach($paths as $rhs) {
                if($lhs !== $rhs && Matrix::fromCollection($lhs)->includes(Matrix::fromCollection($rhs))) {
                    continue 2;
                }
            }
            $resultPaths[] = $lhs;
        }

        $result = [];
        foreach($resultPaths as $path) {
            //$result[] = Matrix::fromCollection($path->getNormalized())->stringify();
            $result[] = Matrix::fromCollection($path)->stringify();
        }
        $result = array_unique($result);

        return $result;
    }
}
