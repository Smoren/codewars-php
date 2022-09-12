<?php

namespace Smoren\Testing\Tests\Unit;

use Smoren\Testing\BreakPieces;
use Smoren\Testing\Drawer;
use Smoren\Testing\Matrix;

ini_set('memory_limit', -1);

class IntervalsTest extends \Codeception\Test\Unit
{
    public function testProblem()
    {
        $shape = implode("\n", [
            "+---+---+---+---+---+---+---+---+",
            "|   |   |   |   |   |   |   |   |",
            "+---+---+---+---+---+---+---+---+",
        ]);
        $actual = (new BreakPieces())->process($shape);
        $a = 1;
    }

    public function testBase()
    {
        $shape = implode("\n", [
            "+------------+",
            "|            |",
            "|            |",
            "|            |",
            "+------+-----+",
            "|      |     |",
            "|      |     |",
            "+------+-----+",
        ]);
        $expected = [
            implode("\n", [
                "+------------+",
                "|            |",
                "|            |",
                "|            |",
                "+------------+",
            ]),
            implode("\n", [
                "+------+",
                "|      |",
                "|      |",
                "+------+",
            ]),
            implode("\n", [
                "+-----+",
                "|     |",
                "|     |",
                "+-----+",
            ]),
        ];
        $actual = (new BreakPieces())->process($shape);
        sort($actual);
        sort($expected);
        $this->assertEquals(json_encode($expected), json_encode($actual));
    }
}
