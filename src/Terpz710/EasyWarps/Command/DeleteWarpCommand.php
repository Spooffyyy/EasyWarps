<?php

declare(strict_types=1);

namespace Terpz710\EasyWarps\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use Terpz710\EasyWarps\Main;

class DeleteWarpCommand extends Command {

    private $dataFolder;

    public function __construct(string $dataFolder) {
        parent::__construct("deletewarp", "Delete a warp location");
        $this->setPermission("easywarps.deletewarp");
        $this->dataFolder = $dataFolder;
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command.");
                return true;
            }

            if (empty($args)) {
                $sender->sendMessage("Usage: /deletewarp <warp>");
                return false;
            }

            $warpName = $args[0];
            $warpData = $this->loadWarpData();

            if (isset($warpData[$warpName])) {
                unset($warpData[$warpName]);
                $this->saveWarpData($warpData);

                $sender->sendMessage("Warp location '$warpName' has been deleted.");
            } else {
                $sender->sendMessage("The warp location '$warpName' does not exist.");
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

    private function saveWarpData(array $warpData): void {
        $config = new Config($this->dataFolder . "warps.yml", Config::YAML);

        $config->set("warps", $warpData);
        $config->save();
    }
}
