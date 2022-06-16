<?php 
namespace PrizeSweeper;
/**
 * Board
 */
class Board
{
    private int $currX, $currY;
    private array $boardArr;
    private array $boardTable;
    private array $prizesArr;
    private array $requestedCellsArr;


    // With prizeDefValue we can determin which cell is a prize cell
    public function __construct(
        protected int $x, 
        protected int $y, 
        protected int $prizesCount, 
        protected int|string $prizeDefValue='*'
    ) {
        $this->fillBoard();
        $this->fillPrizesIntoBoard();
        $this->fillGuideNumbersIntoBoardArray();
    }

    public function getBoardArray(): ?array {
        return $this->boardArr;
    }

    public function getBoardTable(): ?array {
        if (empty($this->boardTable))
            return null;
        return $this->boardTable;
    }

    public function makeBoardTable(): void {
        $this->boardTable=[];
        for ($i=0; $i < count($this->boardArr) ; $i++) { 
            $currXY = $this->getXYFromIndex($i);
            $this->boardTable[$currXY->y][$currXY->x] = $this->boardArr[$i];
        }
    }

    public function printBoardArray(): void {
        print PHP_EOL.'|';
        $currY = 0;
        for ($i=0; $i < $this->x*$this->y; $i++) {
            if (floor($i/$this->x)>$currY) {
                $currY=floor($i/$this->x);
                print '|'.PHP_EOL.'|';
            }
            $value = $this->boardArr[$i]==-1?'*':$this->boardArr[$i];
            print " $value ";
        }
        print '|'.PHP_EOL;
    }

    public function printBoardTable(): void {
        print PHP_EOL;
        for ($i=0; $i < $this->y; $i++) { 
            print '|';
            for ($j=0; $j < $this->x; $j++) { 
                $value = $this->boardTable[$i][$j];
                $value = $value==-1?'*':$value;
                print " $value ";
            }
            print '|'.PHP_EOL;
        }
    }

    public function getCellValue(PositionXY $posXY): mixed {
        $this->requestedCellsArr[] = [$posXY->x, $posXY->y];

        $index = $this->getIndexFromXY($posXY);

        return $this->boardArr[$index];
    }

    public function isCellValid(PositionXY $posXY): bool {
        if ($posXY->x<0 || $posXY->x>$this->x*$this->y-1)
            return false;
        if ($posXY->y<0 || $posXY->y>$this->x*$this->y-1)
            return false;

        if (in_array([$posXY->x, $posXY->y], $this->requestedCellsArr)) {
            return false;
        }
        return true;
    }

    public function getPrizeDefValue(): int|string {
        return $this->prizeDefValue;
    }

    public function getX(): int {
        return $this->x;
    }

    public function getY(): int {
        return $this->y;
    }

    protected function fillBoard(): void {
        $this->requestedCellsArr = [];
        $this->boardArr = array_fill(0, $this->x*$this->y, 0);
        $boardLength = count($this->boardArr);
    }

    protected function fillPrizesIntoBoard(): void {
        if (!$this->boardArr)
            throw new BadFunctionCallException('Board array should be filled first, then you can fill guide numbers.');

        // shuffle is not sorted also if you sort it then you can't get random values
        $this->prizesArr = [];
        $prizeCounter = 0;
        $boardLength = $this->x*$this->y;

        // value should be just the default value so it's not important
        while ($prizeCounter<$this->prizesCount) {
            $rand = rand(0, $boardLength-1);
            if (!in_array($rand, $this->prizesArr)) {
                $this->prizesArr[] = $rand;
                $this->boardArr[$rand] = $this->prizeDefValue;
                $prizeCounter++;
            }
        }
    }

    protected function fillGuideNumbersIntoBoardArray(): void {
        if (!$this->boardArr)
            throw new BadFunctionCallException('Board array should be filled first, then you can fill guide numbers.');

        foreach ($this->prizesArr as $prizeCell) {
            $aroundPoss = $this->findAroundCells($prizeCell);
            foreach ($aroundPoss as $pos) {
                if ($this->boardArr[$pos]!=$this->prizeDefValue)
                    $this->boardArr[$pos]+=1;
            }
        }
    }

    public function getXYFromIndex(int $currentIndex): PositionXY {
        $currY = floor($currentIndex/$this->x);
        $currX = $currentIndex-$currY*$this->x;

        return new PositionXY($currX, $currY);
    }

    public function getIndexFromXY(PositionXY $posXY): int {
        return $posXY->y*$this->x+$posXY->x;
    }

    protected function findAroundCells(int $currentCell): array {
        $aroundPosArray = [];

        $aroundPosOffsetArray = [
            -1*$this->x-1,
            -1*$this->x,
            -1*$this->x+1,
            -1,
            1,
            1*$this->x-1,
            1*$this->x,
            1*$this->x+1,
        ];
        
        $currXY = $this->getXYFromIndex($currentCell);
        foreach ($aroundPosOffsetArray as $offset) {
            // to avoid out of scope cells
            if ($offset+$currentCell<0 || $offset+$currentCell>$this->x*$this->y-1)
                continue;

            // adding +1 or -1 to the currentCell could change the y
            // we don't have y here, but we should consider it 
            
            $posXY = $this->getXYFromIndex($offset+$currentCell);
            if (abs($offset)==1 && abs($currXY->x-$posXY->x)!=1) {
                continue;
            }
            if (abs($offset)>1 && abs($currXY->y-$posXY->y)!=1) {
                continue;
            }
            $aroundPosArray[] = $offset+$currentCell;
        }


        return $aroundPosArray;
    }

    // for 2d arrays, is it more readable?
    protected function findAroundCellsXY(int $currX, int $currY): array {
        $aroundPosArray = [];
        // we can use nested loop to auto generate this
        $aroundPosOffsetArray = [
            [-1, -1],
            [0, -1],
            [1, -1],
            [1, 0],
            [1, 1],
            [0, 1],
            [-1, 1],
            [-1, 0],
        ];

        foreach ($aroundPosOffsetArray as $position) {
            // for x
            if ($position[0]+$currX<0 || $position[0]+$currX>=$this->x)
                continue;
            // for y
            if ($position[1]+$currY<0 || $position[0]+$currY>=$this->y)
                continue;

            $aroundPosArray[] = $position;
        }


        return $aroundPosArray;
    }
}