<?php
namespace SalmonDE\GuessTheNumber;

use InvalidArgumentException;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class NumberGame { // split this up into multiple classes and keep this as parent class

    const UNKNOWN_GAME = 0;
    const RANDOM_INT_GAME = 1;
    const EXPONENT_GAME = 2;
    const ADDITION_GAME = 3;
    const SUBTRACTION_GAME = 4;
    const MULTIPLICATION_GAME = 5;
    const DIVISION_GAME = 6;
    const FACTORIAL_GAME = 7;
    //const PATTERN_SEARCH_GAME = 8; ToDo

    private $gameType = self::UNKNOWN_GAME;
    protected $name;
    protected $example;
    protected $solution = null;
    protected $calculation = null;
    protected $firstInt = null;
    protected $firstIntMin = null;
    protected $firstIntMax = null;
    protected $secondInt = null;
    protected $secondIntMin = null;
    protected $secondIntMax = null;
    protected $itemPrizes = [];

    public function __construct(int $gameType = 0, string $name, string $example, array $options, array $prizes = []){
        if($gameType < 1 and $gameType > 6){
            throw new InvalidArgumentException('Invalid game type specified: '.$gameType);
        }

        if(empty($options)){
            throw new InvalidArgumentException('Options array can\'t be empty!');
        }

        $this->gameType = $gameType;
        $this->name = $name;

        foreach($prizes as $itemString){
            $this->itemPrizes[] = new Item(...explode(':', $itemString));
        }

        $this->initGame($options);
    }

    protected function initGame(array $options){
        switch($this->gameType){
            case self::RANDOM_INT_GAME;
                $this->firstIntMin = (int) $options['min'];
                $this->firstIntMax = (int) $options['max'];

                $this->solution = random_int($this->firstIntMin, $this->firstIntMax);
                $this->firstInt = &$this->solution;
                break;

            case self::EXPONENT_GAME:
                $this->firstIntMin = (int) $options['baseIntMin'];
                $this->firstIntMax = (int) $options['baseIntMax'];
                $this->firstInt = random_int($this->firstIntMin, $this->firstIntMax); // base

                $this->secondIntMin = (int) $options['exponentMin'];
                $this->secondIntMax = (int) $options['exponentMax'];
                $this->secondInt = random_int($this->secondIntMin, $this->secondIntMax); // exponent

                $this->calculation = "({$this->firstInt})^{$this->secondInt}";
                $this->solution = $this->firstInt ** $this->secondInt;
                break;

            case self::ADDITION_GAME:
                $this->firstIntMin = (int) $options['min'];
                $this->firstIntMax = (int) $options['max'];
                $this->firstInt = random_int($this->firstIntMin, $this->firstIntMax);

                $this->secondIntMin = &$this->firstIntMin;
                $this->secondIntMax = &$this->firstIntMax;
                $this->secondInt = random_int($this->secondIntMin, $this->secondIntMax);

                $this->calculation = $this->firstInt.' + '.$this->secondInt;
                $this->solution = $this->firstInt + $this->secondInt;
                break;

            case self::SUBTRACTION_GAME:
                $this->firstIntMin = (int) $options['min'];
                $this->firstIntMax = (int) $options['max'];
                $this->firstInt = random_int($this->firstIntMin, $this->firstIntMax);

                $this->secondIntMin = &$this->firstIntMin;
                $this->secondIntMax = &$this->firstIntMax;
                $this->secondInt = random_int($this->secondIntMin, $options['allowNegativeSolution'] ? $this->secondIntMax : $this->firstInt);

                $this->calculation = $this->firstInt.' - '.$this->secondInt;
                $this->solution = $this->firstInt - $this->secondInt;
                break;

            case self::MULTIPLICATION_GAME:
                $this->firstIntMin = (int) $options['min'];
                $this->firstIntMax = (int) $options['max'];
                $this->firstInt = random_int($this->firstIntMin, $this->firstIntMax);

                $this->secondIntMin = &$this->firstIntMin;
                $this->secondIntMax = &$this->firstIntMax;
                $this->secondInt = random_int($this->secondIntMin, $this->secondIntMax);

                $this->calculation = $this->firstInt.' * '.$this->secondInt;
                $this->solution = $this->firstInt * $this->secondInt;
                break;

            case self::DIVISION_GAME:
                $this->firstIntMin = (int) $options['dividendMin'];
                $this->firstIntMax = (int) $options['dividendMax'];
                $this->firstInt = random_int($this->firstIntMin, $this->firstIntMax);

                $this->secondIntMin = (int) $options['divisorMin'];
                $this->secondIntMax = (int) $options['divisortMax'];
                $this->secondInt = random_int($this->secondIntMin, $this->secondIntMax);

                $this->calculation = $this->firstInt.' / '.$this->secondInt;
                $this->solution = $this->firstInt / $this->secondInt;
                break;

            case self::FACTORIAL_GAME:
                $this->firstIntMin = $options['min'];
                $this->firstIntMax = $options['max'];
                $this->firstInt = random_int($this->firstIntMin, $this->firstIntMax);

                $this->calculation = $this->firstInt.'!';
                $this->solution = gmp_fact($this->firstInt);
                break;

            default:
                $plugin = Server::getInstance()->getPluginManager()->getPlugin('GuessTheNumber');
                $plugin->critical('Unknown game type! Stopping the game ...');
                $plugin->stopGame();
                break;
        }
    }

    final public function getType(): int{
        return $this->gameType;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getFirstNumber(): int{
        return $this->firstInt;
    }

    public function getSecondNumber(): int{
        return $this->secondInt;
    }

    public function getFirstMin(): int{
        return $this->firstMin;
    }

    public function getFirstMax(): int{
        return $this->firstMax;
    }

    public function getSecondMin(): int{
        return $this->secondMin;
    }

    public function getSecondMax(): int{
        return $this->secondMax;
    }

    public function getItemPrizes(): array{
        return $this->itemPrizes;
    }

    public function getExample(): string{
        return $this->example;
    }

    public function getCalculation(): string{
        return $this->calculation;
    }

    public function getSolution(): float{
        return $this->solution;
    }

    public function isSolution(float $number): bool{
        return ((float) $this->solution) == ((float) $number);
    }

    public function setItemPrizes(array $prizes){
        $itemPrizes = [];

        foreach($prizes as $prize){
            if($prize instanceof Item){
                $itemPrizes[] = $prize;
            }
        }

        $this->itemPrizes = $itemPrizes;
    }

    public function givePrizes(Player $player, Main $plugin){
        $prizeListMessage = TF::GREEN.TF::BOLD.$plugin->getMessage('prizeList.header').TF::RESET;

        foreach($this->itemPrizes as $itemPrize){
            $prizeListMessage .= "\n".TF::AQUA.$plugin->getMessage('prizeList.item', $itemPrize->getName(), $itemPrize->getCount()).TF::RESET;

            $player->getInventory()->addItem(clone $itemPrize);
        }

        $player->sendMessage($prizeListMessage);
    }

}
