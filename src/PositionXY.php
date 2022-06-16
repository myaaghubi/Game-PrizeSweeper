<?php 
namespace PrizeSweeper;
/**
 * Position X,Y
 */
class PositionXY {
    public function __construct(
        public readonly int $x,
        public readonly int $y
    ) {
        
    }
}