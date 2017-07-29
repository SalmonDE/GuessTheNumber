<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber;

use InvalidStateException;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\lang\BaseLang;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;
use pocketmine\level\sound\ClickSound;
use SalmonDE\GuessTheNumber\Tasks\AnswerCheckTask;

class Main extends PluginBase implements Listener {

    private $currentGame = null;
    private $timer = 60;
    private $baseLang;
    private $decimalMark = '.';
    private $thousandSeparator = ',';

    public function onEnable(){
        $this->saveResource('config.yml');
        $this->saveResource($this->getConfig()->get('language'));

        $this->timer = (float) $this->getConfig()->get('timer') * 20;
        $this->baseLang = new BaseLang($this->getConfig()->get('language'), $this->getDataFolder());

        $this->decimalMark = $this->baseLang->get('chat.decimalMark');
        $this->thousandSeparator = $this->baseLang->get('chat.thousandSeparator');

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function createGame(int $gameType): NumberGame{
        $allPrizes = $this->getConfig()->get('prizes');

        switch($gameType){
            case NumberGame::RANDOM_INT_GAME:
                $name = $this->getMessage('game.randomInteger');
                $example = $this->getMessage('game.randomInteger.example');
                $options = $this->getConfig()->get('randomInteger');
                $prizes = $allPrizes['randomIntegerItems'];
                break;

            case NumberGame::EXPONENT_GAME:
                $name = $this->getMessage('game.exponent');
                $example = $this->getMessage('game.exponent.example');
                $options = $this->getConfig()->get('exponent');
                $prizes = $allPrizes['exponentItems'];
                break;

            case NumberGame::ADDITION_GAME:
                $name = $this->getMessage('game.addition');
                $example = $this->getMessage('game.addition.example');
                $options = $this->getConfig()->get('addition');
                $prizes = $allPrizes['additionItems'];
                break;

            case NumberGame::SUBTRACTION_GAME:
                $name = $this->getMessage('game.subtraction');
                $example = $this->getMessage('game.subtraction.example');
                $options = $this->getConfig()->get('subtraction');
                $prizes = $allPrizes['subtractionItems'];
                break;

            case NumberGame::MULTIPLICATION_GAME:
                $name = $this->getMessage('game.multiplication');
                $example = $this->getMessage('game.multiplication.example');
                $options = $this->getConfig()->get('multiplication');
                $prizes = $allPrizes['multiplicationItems'];
                break;

            case NumberGame::DIVISION_GAME:
                $name = $this->getMessage('game.division');
                $example = $this->getMessage('game.division.example');
                $options = $this->getConfig()->get('division');
                $prizes = $allPrizes['divisionItems'];
                break;

            case NumberGame::FACTORIAL_GAME:
                $name = $this->getMessage('game.factorial');
                $example = $this->getMessage('game.factorial.example');
                $options = $this->getConfig()->get('factorial');
                $prizes = $allPrizes['factorialItems'];
                break;

            default:
                $name = 'Unknown';
                $options = [];
                $prizes = [];
                $example = '';
        }

        return new NumberGame($gameType, (string) $name, (string) $example, (array) $options, (array) $prizes);
    }

    public function startGame(NumberGame $game): bool{
        if($this->isGameRunning()){
            return false;
        }

        $msg = TF::GREEN.TF::BOLD.$this->getMessage('game.startHeader', $game->getName()).TF::RESET."\n";
        $msg .= $this->getMessage('game.equation', $game->getCalculation()).TF::RESET."\n";
        $msg .= $this->getMessage('game.example', $game->getExample()).TF::RESET."\n";
        $msg .= $this->getMessage('game.howTo').TF::RESET;

        foreach($this->getServer()->getOnlinePlayers() as $player){
            $player->getLevel()->addSound(new ClickSound($player), [$player]);
            $player->addTitle('', $game->getName());
            $player->sendMessage($msg);
        }

        $this->currentGame = $game;

        return true;
    }

    public function stopGame(): bool{
        if($this->isGameRunning()){
            $this->getServer()->broadcastMessage(TF::GOLD.$this->getMessage('general.stop'));

            $this->currentGame = null;

            return true;
        }

        return false;
    }

    public function getCurrentGame(): NumberGame{
        if(!$this->isGameRunning()){
            throw new InvalidStateException('There isn\'t any game running!');
        }

        return $this->currentGame;
    }

    public function isGameRunning(): bool{
        return $this->currentGame instanceof NumberGame;
    }

    public function getTimer(): float{
        return $this->timer;
    }

    public function getMessage(string $index, ...$args): string{
        if(empty($args)){
            return $this->baseLang->get($index);
        }

        return $this->baseLang->translateString($index, $args);
    }

    /*
     * @priority MONITOR
     */

    public function onChat(PlayerChatEvent $event){
        if($this->isGameRunning()){
            if(is_numeric($msg = str_replace([$this->decimalMark, $this->thousandSeparator], [
                        '.', ''], $event->getMessage()))){
                if(!$this->canPlay($event->getPlayer(), $this->getCurrentGame()->getType())){
                    $event->getPlayer()->sendMessage(TF::RED.$this->getMessage('general.notAllowedToPlay'));
                }

                $number = floatval($msg);
                $this->getServer()->getScheduler()->scheduleDelayedTask(new AnswerCheckTask($this, $event->getPlayer(), $number), $this->timer);

                $event->getPlayer()->sendMessage('general.checking', $this->timer / 20);

                $event->setCancelled();
            }
        }
    }

    public function canPlay(Player $player, int $gameType): bool{
        switch($gameType){
            case NumberGame::RANDOM_INT_GAME:
                return $player->hasPermission('guessthenumber.play.randomint');

            case NumberGame::EXPONENT_GAME:
                return $player->hasPermission('guessthenumber.play.exponent');

            case NumberGame::ADDITION_GAME:
                return $player->hasPermission('guessthenumber.play.addition');

            case NumberGame::SUBTRACTION_GAME:
                return $player->hasPermission('guessthenumber.play.subtraction');

            case NumberGame::MULTIPLICATION_GAME:
                return $player->hasPermission('guessthenumber.play.multiplication');

            case NumberGame::DIVISION_GAME:
                return $player->hasPermission('guessthenumber.play.division');

            case NumberGame::FACTORIAL_GAME:
                return $player->hasPermission('guessthenumber.play.factorial');

            default:
                return false;
        }
    }

}
