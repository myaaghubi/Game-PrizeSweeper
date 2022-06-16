<?php declare(strict_types=1);

namespace PrizeSweeper;

require __DIR__ . "/../bootstrap/bootstrap.php";

$prizeSweeper = new PrizeSweeper();
$prizeSweeper->makeACompetition();
$prizeSweeper->runCompetition();