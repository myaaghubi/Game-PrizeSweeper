<?php 
namespace PrizeSweeper;

/**
 * User Class
 */
class User {
    protected int $score;

    public function __construct(
        protected readonly int $id,
        protected readonly string $name
    ) {
        $this->score = 0;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getScore(): int {
        return $this->score;
    }

    // it can be a negative number
    public function addScore(int $score): void {
        $this->score+=$score;
    }

    public function dump(): void {
        var_dump($this->id, $this->name, $this->score);
    }
}