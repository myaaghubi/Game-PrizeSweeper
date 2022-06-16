<?php declare(strict_types=1);

namespace PrizeSweeper;


/**
 * My class to run the game
 */
class PrizeSweeper {
    protected Board $board;
    protected array $users;
    protected array $events;

    public function sayHello(): string {
        return 'Hello World';
    }

    public function makeACompetition(int $usersCount = 2, int $boardX=10, int $boardY=10): void {
        $this->events = ["New competition!"];
        $this->events[] = "usersCount: $usersCount, {$boardX}x{$boardY}";
        $this->makeUsers($usersCount);

        $this->board = new Board($boardX, $boardY, 20);
    }

    public function runCompetition(int $movesLimit=3, int $scoreForPrize=10): void {
        if (!isset($this->board))
            throw new RuntimeException('You should to make a competition first!');


        $this->events[] = "Initiating ...";
        $this->events[] = "movesLimit: $movesLimit, scoreForPrize: $scoreForPrize";

        for ($i=0; $i < $movesLimit; $i++) {
            foreach ($this->users as $user) {
                $posXY = $this->pickACell();
                $this->events[] = "User {$user->getName()} picked $posXY->x,$posXY->y ";

                $cellValue = $this->board->getCellValue($posXY);
                if ($cellValue == $this->board->getPrizeDefValue()) {
                    $user->addScore($scoreForPrize);
                    $this->events[] = "User {$user->getName()} got the prize! +{$scoreForPrize} ";
                } else if ($cellValue>0) {
                    $user->addScore($cellValue);
                    $this->events[] = "The cell shows {$cellValue}, +{$cellValue} ";
                } else {
                    $this->events[] = "The cell is empty! ";
                }
            }
        }

        $this->showResults();
    }

    protected function showResults(): void {
        if (!isset($this->board))
            throw new RuntimeException('You should to make a competition first!');

        foreach ($this->events as $event) {
            print $event.PHP_EOL;
        }

        print PHP_EOL.'Users:'.PHP_EOL;
        foreach ($this->users as $user) {
            $user->dump();
        }

        $this->board->printBoardArray();
    }
    
    protected function pickACell(): PositionXY {
        $reqX = rand(0, $this->board->getX()-1);
        $reqY = rand(0, $this->board->getY()-1);
        while (!$this->board->isCellValid(new PositionXY($reqX, $reqY))) {
            $reqX = rand(0, $this->board->getX()-1);
            $reqY = rand(0, $this->board->getY()-1);
        }

        return new PositionXY($reqX, $reqY);
    }

    protected function makeUsers(int $usersCount): void {
        $this->users = [];
        for ($i=0; $i < $usersCount; $i++) { 
            $this->users[] = new User($i, $this->pickAName().$i);
        }
    }

    protected function pickAName(): string {
        $names = ['Rachel', 'Chandler', 'Monica', 'Ross', 'Joey', 'Phoebe'];
        return $names[array_rand($names)];
    }
}
