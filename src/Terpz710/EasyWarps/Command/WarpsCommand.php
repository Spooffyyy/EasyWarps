<?php

declare(strict_types=1);

namespace Terpz710\EasyWarps\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use Terpz710\EasyWarps\Main;

class WarpsCommand extends Command {

    private $dataFolder;

    public function __construct(string $dataFolder) {
        parent::__construct("warps", "List available warp locations");
        $this->setPermission("easywarps.list");
        $this->dataFolder = $dataFolder;
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command.");
                return true;
            }

            $warps = $this->loadWarpData();

            if (empty($warps)) {
                $sender->sendMessage("There are no available warp locations.");
            } else {
                $warpList = implode(", ", array_keys($warps));
                $sender->sendMessage("Warps: " . $warpList);
            }
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }

    private function loadWarpData(): array {
        $config = new Config($this->dataFolder . "warps.yml", Config::YAML);

        return $config->get("warps", []);
    }
}
