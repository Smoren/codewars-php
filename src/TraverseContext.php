<?php

namespace Smoren\Testing;

class TraverseContext
{
    public Vector $position;
    public Direction $direction;
    public VectorCollection $passed;
    public VectorCollection $points;

    public function __construct(Vector $position, Direction $direction, VectorCollection $passed, VectorCollection $points)
    {
        $this->position = $position;
        $this->direction = $direction;
        $this->passed = $passed;
        $this->points = $points;
    }

    public function __clone()
    {
        $this->position = clone $this->position;
        $this->direction = clone $this->direction;
        $this->passed = clone $this->passed;
        $this->points = clone $this->points;
    }
}
