<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Games;

class FactorialGame extends NumberGame {

    public function __construct(string $name, array $options, array $prizes, string $playPermission, string $startPermission){
        parent::__construct($name, $prizes, $playPermission, $startPermission);

        $this->firstIntMin = $options['min'] > 0 ? $options['min'] : 0;
        $this->firstIntMax = $options['max'];
    }

    public function initGame(){
        $this->firstInt = random_int($this->firstIntMin, $this->firstIntMax);

        $this->calculation = $this->firstInt.'!';
        $this->solution = gmp_fact($this->firstInt);
    }

    public function resetGame(){
        $this->firstInt = null;
        $this->calculation = null;

        $this->solution = null;
    }

    public function getExample(): string{
        do{
            $firstInt = random_int(10, 12);
        }while($firstInt == $this->firstInt);

        return $firstInt.'! : '.$this->dissolveFact($firstInt).' = '.gmp_fact($firstInt);
    }

    static private function dissolveFact(int $int): string{
        if($int < 1){
            return '';
        }

        return $int.($int === 1 ? '' : ' * ').self::dissolveFact($int - 1);
    }

}
