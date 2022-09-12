<?php

namespace Smoren\Testing;

class VectorCollection
{
    /**
     * @var array<Vector>
     */
    protected array $map = [];

    /**
     * VectorCollection constructor.
     * @param array<Vector> $vectors
     */
    public function __construct(array $vectors = [])
    {
        foreach($vectors as $vector) {
            $this->add($vector);
        }
    }

    public function isset(Vector $item): bool
    {
        return isset($this->map[$item->hash()]);
    }

    public function get(Vector $item): ?Vector
    {
        return $this->map[$item->hash()] ?? null;
    }

    public function add(Vector $item): self
    {
        $this->map[$item->hash()] = clone $item;
        return $this;
    }

    public function last(): ?Vector
    {
        return count($this->map) ? end($this->map) : null;
    }

    public function getSubCollection(Vector $newStart, ?int $size = null): VectorCollection
    {
        $items = $this->toArray();
        $start = array_search($newStart, $items);
        return new VectorCollection(array_slice($items, $start, $size));
    }

    public function getNormalized(): VectorCollection
    {
        [$minBound] = $this->getBounds();
        $collection = clone $this;
        foreach($collection->map as $vector) {
            $vector->x -= $minBound->x;
            $vector->y -= $minBound->y;
        }
        return $collection;
    }

    /**
     * @return Vector[]
     */
    public function getBounds(): array
    {
        $minX = INF;
        $maxX = -INF;
        $minY = INF;
        $maxY = -INF;

        foreach($this->map as $vector) {
            if($vector->x < $minX) {
                $minX = $vector->x;
            }
            if($vector->x > $maxX) {
                $maxX = $vector->x;
            }
            if($vector->y < $minY) {
                $minY = $vector->y;
            }
            if($vector->y > $maxY) {
                $maxY = $vector->y;
            }
        }

        return [new Vector($minX, $minY), new Vector($maxX, $maxY)];
    }

    public function getArea(): float
    {
        $points = $this->toArray();
        if(count($points)) {
            $points[] = $points[0];
        }
        $result = 0;
        $i=0;
        while(isset($points[$i]) && isset($points[$i+1])) {
            [$lhs, $rhs] = [$points[$i], $points[$i+1]];
            $result += $lhs->x*$rhs->y - $lhs->y*$rhs->x;
            ++$i;
        }
        return abs($result/2);
    }

    /**
     * @return array<Vector>
     */
    public function toArray(): array
    {
        return array_values($this->map);
    }

    public function toString(): string
    {
        $points = array_map(function($point) {
            return "({$point->x};{$point->y})";
        }, $this->map);
        return implode(' ', $points);
    }

    public function __clone()
    {
        $map = [];
        foreach($this->map as $hash => $vector) {
            $map[$hash] = clone $vector;
        }
        $this->map = $map;
    }
}
