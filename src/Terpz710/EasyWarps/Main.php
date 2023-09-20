<?php

declare(strict_types=1);

namespace Terpz710\EasyWarps;

use pocketmine\plugin\PluginBase;
use isTerpz710\EasyWarps\Command\SetWarpCommand;
use isTerpz710\EasyWarps\Command\DeleteWarpCommand;
use isTerpz710\EasyWarps\Command\WarpsCommand;
use isTerpz710\EasyWarps\Command\WarpCommand;

class Main extends PluginBase {

    public function onEnable(): void {
        
        $this->getServer()->getCommandMap()->register("setwarp", new SetWarpCommand($this));
        $this->getServer()->getCommandMap()->register("delwarp", new DeleteWarpCommand($this));
        $this->getServer()->getCommandMap()->register("warps", new WarpsCommand($this));
        $this->getServer()->getCommandMap()->register("warp", new WarpCommand($this));
    }
}
