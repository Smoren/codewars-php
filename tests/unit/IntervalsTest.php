<?php

namespace Smoren\Testing\Tests\Unit;

ini_set('memory_limit', -1);

class IntervalsTest extends \Codeception\Test\Unit
{
    public function testProblem()
    {
        $shape = implode("\n", ["+------------+",
            "|            |",
            "|            |",
            "|            |",
            "+------+-----+",
            "|      |     |",
            "|      |     |",
            "+------+-----+"]);
        $expected = [implode("\n", ["+------------+",
            "|            |",
            "|            |",
            "|            |",
            "+------------+"]),
            implode("\n", ["+------+",
                "|      |",
                "|      |",
                "+------+"]),
            implode("\n", ["+-----+",
                "|     |",
                "|     |",
                "+-----+"])];
        $actual = (new BreakPieces())->process($shape);
        sort($actual);
        sort($expected);
        $this->assertEquals(json_encode($expected), json_encode($actual));
    }
}

class BreakPieces {
    public function process($shape) {
        $m = new Matrix($shape);
        $dm = new DirectionManager();
        $pc = new Collection();
        $lc = new Collection();
        $walkers = [new Walker($m->getFirstPosition(), new Direction(1, 0))];
        while(count($walkers) > 0) {
            $walker = array_pop($walkers);
            if($m->getValue($walker->position) === '+') {
                if($pc->isset($walker->position)) {
                    continue;
                }
                $pc->add($walker->position);
            }
            $possibleDirections = $dm->getPossibleDirections($walker->position, $walker->direction, $m);
            foreach($possibleDirections as $d) {
                if(!$d->equals($walker->direction)) {
                    $curWalker = clone $walker;
                    $curWalker->direction = $d;
                } else {
                    $curWalker = $walker;
                }
                $curWalker->go();
                $walkers[] = $curWalker;
            }
        }
        return [];
    }
}

class Walker {
    public Vector $position;
    public Direction $direction;

    public function __construct(Vector $p, Direction $d)
    {
        $this->position = $p;
        $this->direction = $d;
    }

    public function go(): void {
        $this->position->x += $this->direction->x;
        $this->position->y += $this->direction->y;
    }

    public function __clone() {
        $this->position = clone $this->position;
        $this->direction = clone $this->direction;
    }
}

class DirectionManager {
    /**
     * @return array<Direction>
     */
    public function getPossibleDirections(Vector $p, Direction $d, Matrix $m): array {
        $result = [];
        $inv = $d->inverse();
        $cd = clone $d;

        do {
            $cd->turnRight();
            $nextPos = $this->getNextPosition($p, $cd);
            if(!$cd->equals($inv) && $m->getValue($nextPos) !== false) {
                $result[] = clone $cd;
            }
        } while(!$cd->equals($d));

        return $result;
    }

    public function getNextPosition(Vector $p, Direction $d): Vector {
        return new Vector($p->x+$d->x, $p->y+$d->y);
    }
}

class Matrix {
    protected array $storage;

    public function __construct(string $shape) {
        $this->storage = array_map(function($row) {
            return str_split($row);
        }, explode("\n", $shape));
    }

    public function getValue(Vector $coords) {
        [$x, $y] = [$coords->x, $coords->y];
        if(!isset($this->storage[$y][$x]) || $this->storage[$y][$x] === ' ') {
            return false;
        }
        return $this->storage[$y][$x];
    }

    public function getFirstPosition(): Vector {
        return new Vector((int)array_search('+', $this->storage[0]), 0);
    }
}

interface Hashable {
    public function hash(): string;
}

class Vector implements Hashable {
    public int $x;
    public int $y;

    public function __construct(int $x, int $y) {
        $this->x = $x;
        $this->y = $y;
    }

    public function len(): int {
        return sqrt($this->x**2 + $this->y**2);
    }

    public function equals(Vector $with): bool {
        return $this->x === $with->x && $this->y === $with->y;
    }

    public function hash(): string {
        return "{$this->x}-{$this->y}";
    }

    public function inverse(): Vector {
        return new Vector(-$this->x, -$this->y);
    }
}

class Line implements Hashable {
    public Vector $start;
    public Vector $end;

    public function __construct(Vector $lhs, Vector $rhs) {
        if($lhs->len() < $rhs->len()) {
            $this->start = $lhs;
            $this->end = $rhs;
        } elseif($lhs->len() > $rhs->len()) {
            $this->start = $rhs;
            $this->end = $lhs;
        } elseif($lhs->x < $rhs->x) {
            $this->start = $lhs;
            $this->end = $rhs;
        } else {
            $this->start = $rhs;
            $this->end = $lhs;
        }
    }

    public function len(): int {
        return sqrt(($this->end->x - $this->start->x)**2 + ($this->end->y - $this->start->y)**2);
    }

    public function hash(): string {
        return "{$this->start->hash()}-{$this->end->hash()}";
    }
}

class Direction extends Vector {
    public function turnRight() {
        $buf = $this->x;
        $this->x = -$this->y;
        $this->y = $buf;
    }
}

class Collection {
    protected array $map = [];

    public function isset(Hashable $item): bool {
        return isset($this->map[$item->hash()]);
    }

    public function get(Hashable $item): ?Hashable {
        return $this->map[$item->hash()] ?? null;
    }

    public function add(Hashable $item): Hashable {
        if(!$this->isset($item)) {
            $this->map[$item->hash()] = clone $item;
        }
        return $this->map[$item->hash()];
    }
}