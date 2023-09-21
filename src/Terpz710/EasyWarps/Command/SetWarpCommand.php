<?php

declare(strict_types=1);

namespace Terpz710\EasyWarps\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use Terpz710\EasyWarps\Main;

class SetWarpCommand extends Command {

    private $dataFolder;

    public function __construct(string $dataFolder) {
        parent::__construct("setwarp", "Set a warp");
        $this->setPermission("easywarp.setwarp");
        $this->dataFolder = $dataFolder;
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command.");
                return true;
            }

            if (empty($args)) {
                $sender->sendMessage("Usage: /setwarp §a<warp>");
                return false;
            }

            $warpName = $args[0];
            $position = $sender->getPosition();

            $warpLocation = [
                'x' => $position->getX(),
                'y' => $position->getY(),
                'z' => $position->getZ(),
                'world' => $sender->getWorld()->getFolderName(),
            ];

            $this->saveWarpData($warpName, $warpLocation);

            $sender->sendMessage("Warp§4 $warpName §rhas been set!");
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }

    private function saveWarpData(string $warpName, array $warpLocation): void {
        $config = new Config($this->dataFolder . "warps.yml", Config::YAML);

        $warps = $config->get("warps", []);
        $warps[$warpName] = $warpLocation;

        $config->set("warps", $warps);
        $config->save();
    }
}
