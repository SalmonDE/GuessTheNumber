<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Games;

class DivisionGame extends NumberGame {

    public function __construct(string $name, array $options, array $prizes, string $playPermission, string $startPermission){
        parent::__construct($name, $prizes, $playPermission, $startPermission);

        $this->firstIntMin = (int) $options['dividendMin'];
        $this->firstIntMax = (int) $options['dividendMax'];

        $this->secondIntMin = (int) $options['divisorMin'];
        $this->secondIntMax = (int) $options['divisorMax'];
    }

    public function initGame(): void{
        $this->firstInt = random_int($this->firstIntMin, $this->firstIntMax);
        $this->secondInt = random_int($this->secondIntMin, $this->secondIntMax);

        $this->calculation = $this->firstInt.' / '.$this->secondInt;
        $this->solution = round($this->firstInt / $this->secondInt, 2);
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

        return $firstInt.' / '.$secondInt.' = '.($firstInt / $secondInt);
    }
}
