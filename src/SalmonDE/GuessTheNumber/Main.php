<?php
declare(strict_types = 1);
namespace SalmonDE\GuessTheNumber;

use InvalidStateException;
use pocketmine\lang\BaseLang;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\GuessTheNumber\Commands\NumberGameCmd;
use SalmonDE\GuessTheNumber\Events\NumberGameRegisterEvent;
use SalmonDE\GuessTheNumber\Events\NumberGameStartEvent;
use SalmonDE\GuessTheNumber\Events\NumberGameStopEvent;
use SalmonDE\GuessTheNumber\Games\AdditionGame;
use SalmonDE\GuessTheNumber\Games\DivisionGame;
use SalmonDE\GuessTheNumber\Games\ExponentGame;
use SalmonDE\GuessTheNumber\Games\FactorialGame;
use SalmonDE\GuessTheNumber\Games\MultiplicationGame;
use SalmonDE\GuessTheNumber\Games\NumberGame;
use SalmonDE\GuessTheNumber\Games\RandomIntegerGame;
use SalmonDE\GuessTheNumber\Games\SubtractionGame;

class Main extends PluginBase {

    private $currentGame = null;
    private $timer = 60;
    private $baseLang;
    private $decimalMark = '.';
    private $thousandSeparator = ',';
    private $listener = null;
    private $answeringPlayers = [];
    private $gameTypes = [];

    public function onEnable(): void{
        $this->saveResource('config.yml');
        $this->saveResource('eng.ini'); // fallback
        $this->saveResource($this->getConfig()->get('language').'.ini');

        $this->timer = (int) $this->getConfig()->get('timer') * 20;
        $this->baseLang = new BaseLang($this->getConfig()->get('language'), $this->getDataFolder(), 'eng');

        $this->decimalMark = $this->baseLang->get('chat.decimalMark');
        $this->thousandSeparator = $this->baseLang->get('chat.thousandSeparator');

        $this->getServer()->getCommandMap()->register('guessthenumber', new NumberGameCmd($this), 'numbergame');

        $this->registerGames();

        $this->getServer()->getPluginManager()->registerEvents($this->listener ?? $this->listener = new MainListener($this), $this);
    }

    private function registerGames(): void{
        $allPrizes = $this->getConfig()->get('prizes');

        $name = $this->getMessage('game.randomInteger');
        $options = $this->getConfig()->get('randomInteger');
        $prizes = $allPrizes['randomIntegerItems'];

        $this->registerGame(RandomIntegerGame::class, $name, $options, $prizes, 'guessthenumber.play.randomint', 'guessthenumber.cmd.randomint');

        $name = $this->getMessage('game.exponent');
        $options = $this->getConfig()->get('exponent');
        $prizes = $allPrizes['exponentItems'];

        $this->registerGame(ExponentGame::class, $name, $options, $prizes, 'guessthenumber.play.exponent', 'guessthenumber.cmd.exponent');

        $name = $this->getMessage('game.addition');
        $options = $this->getConfig()->get('addition');
        $prizes = $allPrizes['additionItems'];

        $this->registerGame(AdditionGame::class, $name, $options, $prizes, 'guessthenumber.play.addition', 'guessthenumber.cmd.addition');

        $name = $this->getMessage('game.subtraction');
        $options = $this->getConfig()->get('subtraction');
        $prizes = $allPrizes['subtractionItems'];

        $this->registerGame(SubtractionGame::class, $name, $options, $prizes, 'guessthenumber.play.subtraction', 'guessthenumber.cmd.subtraction');

        $name = $this->getMessage('game.multiplication');
        $options = $this->getConfig()->get('multiplication');
        $prizes = $allPrizes['multiplicationItems'];

        $this->registerGame(MultiplicationGame::class, $name, $options, $prizes, 'guessthenumber.play.multiplication', 'guessthenumber.cmd.multiplcation');

        $name = $this->getMessage('game.division');
        $options = $this->getConfig()->get('division');
        $prizes = $allPrizes['divisionItems'];

        $this->registerGame(DivisionGame::class, $name, $options, $prizes, 'guessthenumber.play.division', 'guessthenumber.cmd.division');

        $name = $this->getMessage('game.factorial');
        $options = $this->getConfig()->get('factorial');
        $prizes = $allPrizes['factorialItems'];

        $this->registerGame(FactorialGame::class, $name, $options, $prizes, 'guessthenumber.play.factorial', 'guessthenumber.cmd.factorial');
    }

    public function registerGame(string $class, string $name, array $options, array $prizes = [], string $playPermission = 'guessthenumber.play', string $startPermission = 'guessthenumber.cmd'): bool{
        $game = new $class($name, $options, $prizes, $playPermission, $startPermission);

        $this->getServer()->getPluginManager()->callEvent($event = new NumberGameRegisterEvent($this, $game));

        if(!$event->isCancelled()){
            $this->gameTypes[str_replace(' ', '', strtolower($name))] = $game;

            $this->getServer()->getPluginCommand('numbergame')->updateAvailableGames();
            return true;
        }

        return false;
    }

    public function startGame(NumberGame $game): bool{
        if($this->isGameRunning()){
            return false;
        }

        $game->initGame();

        $this->getServer()->getPluginManager()->callEvent($event = new NumberGameStartEvent($this, $game));

        if(!$event->isCancelled()){
            $game->announceGame($this);
            $this->currentGame = $game;

            return true;
        }

        $game->resetGame();

        return false;
    }

    public function stopGame(): bool{
        if($this->isGameRunning()){
            $this->getServer()->getPluginManager()->callEvent(new NumberGameStopEvent($this, $this->getCurrentGame()));

            $this->getScheduler()->cancelAllTasks();
            $this->answeringPlayers = [];

            $this->getServer()->broadcastMessage(TF::GOLD.$this->getMessage('general.game.stop'));

            $this->getCurrentGame()->resetGame();
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

    public function gameExists(string $name): bool{
        return $this->getGameByName($name) instanceof NumberGame;
    }

    public function getGameByName(string $name): ?NumberGame{
        return $this->gameTypes[str_replace(' ', '', strtolower($name))] ?? null;
    }

    public function getGames(): array{
        return $this->gameTypes;
    }

    public function isGameRunning(): bool{
        return $this->currentGame instanceof NumberGame;
    }

    public function getTimer(): int{
        return $this->timer;
    }

    public function getListener(): MainListener{
        return $this->listener;
    }

    public function getMessage(string $index, ...$args): string{
        return $this->baseLang->translateString($index, $args);
    }

    public function getDecimalMark(): string{
        return $this->decimalMark;
    }

    public function getThousandSeparator(): string{
        return $this->thousandSeparator;
    }
}
