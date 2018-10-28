<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Games;

class AdditionGame extends NumberGame {

    public function __construct(string $name, array $options, array $prizes, string $playPermission, string $startPermission){
        parent::__construct($name, $prizes, $playPermission, $startPermission);

        $this->firstIntMin = (int) $options['min'];
        $this->firstIntMax = (int) $options['max'];

        $this->secondIntMin = &$this->firstIntMin;
        $this->secondIntMax = &$this->firstIntMax;
    }

    public function initGame(): void{
        $this->firstInt = random_int($this->firstIntMin, $this->firstIntMax);
        $this->secondInt = random_int($this->secondIntMin, $this->secondIntMax);

        $this->calculation = $this->firstInt.' + '.$this->secondInt;
        $this->solution = $this->firstInt + $this->secondInt;
    }

    public function resetGame(): void{
        $this->firstInt = null;
        $this->secondInt = null;

        $this->calculation = null;
        $this->solution = null;
    }

    public function getExample(): string{
        do{
            $firstInt = random_int($this->firstIntMin, $this->firstIntMax);
        }while($firstInt === $this->firstInt);

        do{
            $secondInt = random_int($this->secondIntMin, $this->secondIntMax);
        }while($secondInt === $this->secondInt);

        return $firstInt.' + '.$secondInt.' = '.($firstInt + $secondInt);
    }
}
