<?php

declare(strict_types=1);

namespace Terpz710\EasyWarps\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use Terpz710\EasyWarps\Main;

class WarpCommand extends Command {

    private $dataFolder;

    public function __construct(string $dataFolder) {
        parent::__construct("warp", "Teleport to a warp location");
        $this->setPermission("easywarps.warp");
        $this->dataFolder = $dataFolder;
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command.");
                return true;
            }

            if (empty($args)) {
                $sender->sendMessage("Usage: /warp <warp>");
                return false;
            }

            $warpName = $args[0];
            $warpLocation = $this->loadWarpData($warpName);

            if ($warpLocation !== null) {
                $x = $warpLocation['x'];
                $y = $warpLocation['y'];
                $z = $warpLocation['z'];
                $worldName = $warpLocation['world'];

                $world = $sender->getServer()->getWorldManager()->getWorldByName($worldName);

                if ($world !== null) {
                    $warpVector = new Vector3($x, $y, $z);
                    $sender->teleport($warpVector);
                    $sender->sendMessage("Teleported to warp location '$warpName'.");
                } else {
                    $sender->sendMessage("The world of the warp location no longer exists.");
                }
            } else {
                $sender->sendMessage("The warp location '$warpName' does not exist.");
            }
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }

    private function loadWarpData(string $warpName): ?array {
        $config = new Config($this->dataFolder . "warps.yml", Config::YAML);

        $warps = $config->get("warps", []);
        return $warps[$warpName] ?? null;
    }
}
