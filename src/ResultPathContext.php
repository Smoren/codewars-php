<?php

namespace Smoren\Testing;

class ResultPathContext
{
    public VectorCollection $path;
    public Direction $direct;
    public Direction $reverse;

    public function __construct(VectorCollection $path)
    {
        $this->path = $path;
        $points = $path->toArray();
        $this->direct = $this->getDirection($points[0], $points[1]);
        $this->reverse = $this->getDirection($points[0], $points[count($points)-1]);
    }
    public function hash(): string
    {
        return "{$this->path->toArray()[0]->hash()}-{$this->direct->hash()}-{$this->reverse->hash()}";
    }

    protected function getDirection(Vector $lhs, Vector $rhs): Direction
    {
        $dx = $this->getSign($rhs->x - $lhs->x);
        $dy = $this->getSign($rhs->y - $lhs->y);

        return new Direction($dx, $dy);
    }

    protected function getSign(int $value): int
    {
        if($value > 0) {
            return 1;
        } elseif($value < 0) {
            return -1;
        } else {
            return 0;
        }
    }
}