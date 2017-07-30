<?php
namespace SalmonDE\GuessTheNumber\Games;

use pocketmine\item\Item;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\FizzSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\GuessTheNumber\Events\PlayerFailEvent;
use SalmonDE\GuessTheNumber\Events\PlayerWinEvent;
use SalmonDE\GuessTheNumber\Main;

abstract class NumberGame {

    private $name;
    protected $playPermission;
    protected $startPermission;
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

    public function __construct(string $name, string $example, array $prizes, string $playPermission, string $startPermission){
        $this->name = $name;
        $this->playPermission = $playPermission;
        $this->startPermission = $startPermission;
        $this->example = $example;

        foreach($prizes as $itemString){
            $this->itemPrizes[] = new Item(...explode(':', $itemString));
        }
    }

    abstract public function initGame();

    abstract public function resetGame();

    public function announceGame(Main $plugin, array $recipients = null){
        $msg = TF::GREEN.TF::BOLD.$this->getMessage('game.startHeader', $this->getName()).TF::RESET."\n";
        $msg .= $plugin->getMessage('game.equation', $this->getCalculation()).TF::RESET."\n";
        $msg .= $plugin->getMessage('game.example', $this->getExample()).TF::RESET."\n";
        $msg .= $plugin->getMessage('game.howTo').TF::RESET;

        $sound = new ClickSound(new Vector3());

        foreach($recipients ?? $plugin->getServer()->getOnlinePlayers() as $player){
            $sound->x = $player->z;
            $sound->y = $player->z;
            $sound->z = $player->z;

            $player->getLevel()->addSound($sound, [$player]);
            $player->addTitle('', $this->getName());
            $player->sendMessage($msg);
        }
    }

    final public function getName(): string{
        return $this->name;
    }

    final public function getPlayPermission(): string{
        return $this->playPermission;
    }

    final public function getStartPermission(): string{
        return $this->startPermission;
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

    public function getSolution(): string{
        return (string) $this->solution;
    }

    public function isValidAnswer(string $answer, string $decimalMark, string $thousandSeparator): bool{
        return is_numeric($msg = str_replace([$decimalMark, $thousandSeparator], ['.', ''], $answer));
    }

    public function checkAnswer(string $answer, Player $player, Main $plugin): bool{
        if($this->isSolution($answer)){
            $plugin->getServer()->getPluginManager()->callEvent($event = new PlayerWinEvent($plugin, $this, $player, $answer));

            if(!$event->isCancelled()){
                $msg = TF::GREEN.$plugin->getMessage('answer.right', $answer);

                $sound = new FizzSound(new Vector3());

                foreach($plugin->getServer()->getOnlinePlayers() as $p){
                    $sound->x = $p->x;
                    $sound->y = $p->y;
                    $sound->z = $p->z;

                    $p->sendMessage($msg);
                    $p->getLevel()->addSound(new FizzSound($p), [$p]);
                }

                $this->givePrizes($player, $plugin);
                return true;
            }
        }

        $plugin->getServer()->getPluginManager()->callEvent(new PlayerFailEvent());

        $player->sendMessage(TF::RED.$plugin->getMessage('answer.wrong'));
        $player->getLevel()->addSound(new AnvilFallSound($player), [$player]);

        return false;
    }

    public function isSolution(string $answer): bool{
        return ((string) $this->solution) == ((string) $answer);
    }

    public function setItemPrizes(array $itemPrizes){
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
