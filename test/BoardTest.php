<?php declare(strict_types=1);

namespace MyPackage;

class BoardTest extends \PHPUnit\Framework\TestCase {
    protected Board $board;

    public function setUp(): void {
        $this->x = 8;
        $this->y = 8;
        $this->prizesCount = 15;

        $this->board = new Board($this->x, $this->y, $this->prizesCount);
    }

    public function testGetBoardArray(): void {
        $boardArray = $this->board->getBoardArray();
        $this->assertCount($this->y*$this->x, $boardArray);

        $arrayValues = array_count_values($boardArray);
        $this->assertGreaterThan(1, count($arrayValues));

        $this->assertArrayHasKey($this->board->getPrizeDefValue(), $arrayValues);

        $this->assertEquals($this->prizesCount, $arrayValues[$this->board->getPrizeDefValue()]);
    }

    public function testGetBoardTable(): void {
        $boardTable = $this->board->getBoardTable();
        $this->assertNull($boardTable);

        $this->board->makeBoardTable();
        $boardTable = $this->board->getBoardTable();
        $this->assertNotNull($boardTable);

        $this->assertCount($this->y, $boardTable);

        $array1D = [];
        foreach ($boardTable as $array) {
            // simple merge for two arrays
            $array1D = [...$array1D, ...$array];
        }
        $this->assertCount($this->y*$this->x, $array1D);

        $arrayValues = array_count_values($array1D);
        $this->assertGreaterThan(1, count($arrayValues));

        $this->assertArrayHasKey($this->board->getPrizeDefValue(), $arrayValues);

        $this->assertEquals($this->prizesCount, $arrayValues[$this->board->getPrizeDefValue()]);
    }

    public function testGetIndexFromXY(): void {
        $posXY = new PositionXY(0, 0);
        $currIndex = $this->board->getIndexFromXY($posXY);
        $this->assertEquals(0, $currIndex);

        $posXY = new PositionXY($this->x-1, 0);
        $currIndex = $this->board->getIndexFromXY($posXY);
        $this->assertEquals($this->x-1, $currIndex);

        $posXY = new PositionXY($this->x, 0);
        $currIndex = $this->board->getIndexFromXY($posXY);
        $this->assertEquals($this->x, $currIndex);

        $posXY = new PositionXY(3, 3);
        $currIndex = $this->board->getIndexFromXY($posXY);
        $this->assertEquals(3*$this->x+3, $currIndex);

        // indexes starts from zero
        $posXY = new PositionXY($this->x-1, $this->y-1);
        $currIndex = $this->board->getIndexFromXY($posXY);
        $this->assertEquals($this->x*$this->y-1, $currIndex);
    }

    public function testGetXYFromIndex(): void {
        $index = 0;
        $targXY = new PositionXY(0, 0);
        $currXY = $this->board->getXYFromIndex($index);
        $this->assertEquals($targXY, $currXY);

        $index = $this->x-1;
        $targXY = new PositionXY($index, 0);
        $currXY = $this->board->getXYFromIndex($index);
        $this->assertEquals($targXY, $currXY);

        $index = $this->x;
        $targXY = new PositionXY(0, 1);
        $currXY = $this->board->getXYFromIndex($index);
        $this->assertEquals($targXY, $currXY);

        // it means y=2, x=2
        $index = $this->x*2 + 2;
        $targXY = new PositionXY(2, 2);
        $currXY = $this->board->getXYFromIndex($index);
        $this->assertEquals($targXY, $currXY);
    }

}