<?php

namespace Smoren\Testing;

class Matrix
{
    protected array $storage;

    public static function empty(int $xSize, int $ySize): Matrix
    {
        $rows = [];
        for($i=0; $i<$ySize; ++$i) {
            $rows[] = str_repeat(' ', $xSize);
        }
        return new Matrix(implode("\n", $rows));
    }

    public static function fromCollection(VectorCollection $collection): Matrix
    {
        [, $maxBound] = $collection->getBounds();
        $matrix = Matrix::empty($maxBound->x+1, $maxBound->y+1);

        /** @var Vector[] $points */
        $points = $collection->toArray();
        if(count($points)) {
            $points[] = $points[0];
        }
        $i = 0;

        while(isset($points[$i]) && isset($points[$i+1])) {
            $dx = $points[$i+1]->x - $points[$i]->x;
            $dy = $points[$i+1]->y - $points[$i]->y;
            $xStep = $dx >= 0 ? 1 : -1;
            $yStep = $dy >= 0 ? 1 : -1;
            $x = $points[$i]->x;
            $y = $points[$i]->y;

            if($dx !== 0) {
                while($x !== $points[$i+1]->x) {
                    $matrix->setValue(new Vector($x, $y), '-');
                    $x += $xStep;
                }
            } else {
                while($y !== $points[$i+1]->y) {
                    $matrix->setValue(new Vector($x, $y), '|');
                    $y += $yStep;
                }
            }
            $matrix->setValue($points[$i], '+');

            ++$i;
        }

        return $matrix;
    }

    public function __construct(string $shape)
    {
        $this->storage = array_map(function($row) {
            return str_split($row);
        }, explode("\n", $shape));
    }

    public function getValue(Vector $coords)
    {
        [$x, $y] = [$coords->x, $coords->y];
        if(!isset($this->storage[$y][$x]) || !$this->storage[$y][$x] || $this->storage[$y][$x] === ' ') {
            return false;
        }
        return $this->storage[$y][$x];
    }

    public function setValue(Vector $coords, string $value): void
    {
        [$x, $y] = [$coords->x, $coords->y];
        $this->storage[$y][$x] = $value;
    }

    public function getFirstPosition(): ?Vector
    {
        foreach($this->storage as $y => $row) {
            foreach($row as $x => $value) {
                if($value === '+') {
                    return new Vector($x, $y);
                }
            }
        }

        return null;
    }

    /**
     * @return array<Direction>
     */
    public function getPossibleDirections(Vector $pos, Direction $dir): array
    {
        $result = [];
        $inv = $dir->inverse();
        $curDir = clone $dir;

        do {
            $curDir = $curDir->turnRight();
            $nextPos = $pos->add($curDir);
            if(!$curDir->equals($inv) && $this->getValue($nextPos) !== false) {
                $result[] = clone $curDir;
            }
        } while(!$curDir->equals($dir));

        return $result;
    }

    public function stringify(): string
    {
        $rows = array_map(function($row) {
            return rtrim(implode('', $row));
        }, $this->storage);
        return implode("\n", $rows);
    }
}
