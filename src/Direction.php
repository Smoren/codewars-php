<?php

namespace Smoren\Testing;

class Direction extends Vector
{
    public function turnRight(): Direction
    {
        return new Direction(-$this->y, $this->x);
    }
}
