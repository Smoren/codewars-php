<?php

namespace Smoren\Testing;

class PathContext
{
    public VectorCollection $passed;
    public VectorCollection $points;

    public function __construct(VectorCollection $passed, VectorCollection $points)
    {
        $this->passed = $passed;
        $this->points = $points;
    }

    public function __clone()
    {
        $this->passed = clone $this->passed;
        $this->points = clone $this->points;
    }
}
