<?php

/**
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpIllegalPsrClassPathInspection
 * @noinspection PhpDocSignatureInspection
 * @noinspection PhpDocMissingReturnTagInspection
 * @noinspection SpellCheckingInspection
 * @noinspection PhpUnusedParameterInspection
 */

declare(strict_types=1);

namespace kim\present\idviewer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

use function file_exists;
use function mkdir;

final class IdViewer extends PluginBase implements Listener{
    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    protected function onDisable() : void{
        $dataFolder = $this->getDataFolder();
        if(!file_exists($dataFolder)){
            mkdir($dataFolder);
        }
        $this->getConfig()->save();
    }

    /**
     * @priority MONITOR
     * @handleCancelled true
     */
    public function onPlayerInteract(PlayerInteractEvent $event) : void{
        $player = $event->getPlayer();
        if(!$this->isViewer($player))
            return;

        $block = $event->getBlock();
        $item = $block->asItem();
        $player->sendPopup(TextFormat::AQUA . "{$block->getName()} ({$block->getId()}:{$block->getMeta()}) / asItem({$item->getId()}:{$item->getMeta()})");
    }

    /**
     * @priority MONITOR
     * @handleCancelled true
     */
    public function PlayerItemHeld(PlayerItemHeldEvent $event) : void{
        $player = $event->getPlayer();
        if(!$this->isViewer($player))
            return;

        $item = $event->getItem();
        $player->sendPopup(TextFormat::AQUA . "{$item->getName()} ({$item->getId()}:{$item->getMeta()})");
    }

    /** @param string[] $args */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "It can only be used in-game");
            return true;
        }

        if($this->isViewer($sender)){
            $this->removeViewer($sender);
            $sender->sendMessage(TextFormat::AQUA . "[IdViewer] Disable id viewer");
        }else{
            $this->addViewer($sender);
            $sender->sendMessage(TextFormat::AQUA . "[IdViewer] Enable id viewer");
        }
        return true;
    }

    public function isViewer(Player $player) : bool{
        return (bool) $this->getConfig()->get($player->getXuid(), false);
    }

    public function addViewer(Player $player) : void{
        $this->getConfig()->set($player->getXuid(), true);
    }

    public function removeViewer(Player $player) : void{
        $this->getConfig()->remove($player->getXuid());
    }
}