<?php

namespace Smoren\Testing;

class Vector {
    public int $x;
    public int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function len(): int
    {
        return sqrt($this->x**2 + $this->y**2);
    }

    public function equals(Vector $with): bool
    {
        return $this->x === $with->x && $this->y === $with->y;
    }

    public function inverse(): Vector
    {
        return new Vector(-$this->x, -$this->y);
    }

    public function add(Vector $v): Vector
    {
        return new Vector($this->x+$v->x, $this->y+$v->y);
    }

    public function hash(): string
    {
        return "{$this->x}-{$this->y}";
    }
}
