<?php

declare(strict_types=1);

namespace Terpz710\EasyWarps\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use Terpz710\EasyWarps\Main;

class WarpCommand extends Command {

    private $plugin;

    public function __construct(Plugin $plugin) {
        parent::__construct("warp", "Warp to a specified location", "/warp <warpName>");
        $this->plugin = $plugin;
        $this->setPermission("easywarp.warp");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (count($args) === 1) {
                $warpName = strtolower($args[0]);

                if ($this->warpExists($warpName)) {
                    $warpLocation = $this->getWarpLocation($warpName);

                    if ($warpLocation !== null) {
                        $warpPermission = "easywarp.warp.$warpName";

                        // Check if the player is OP or has the appropriate permission
                        if ($sender->isOp() || $sender->hasPermission($warpPermission)) {
                            $this->teleportToWarp($sender, $warpLocation);
                            $sender->sendMessage(TextFormat::GREEN . "Warped to '$warpName'!");
                            return true;
                        } else {
                            $sender->sendMessage(TextFormat::RED . "You do not have permission to warp to '$warpName'.");
                        }
                    }
                } else {
                    $sender->sendMessage(TextFormat::RED . "Warp '$warpName' does not exist.");
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "Usage: /warp <warpName>");
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "You must be a player to use this command.");
        }

        return false;
    }

    private function warpExists(string $warpName): bool {
        $config = new Config($this->plugin->getDataFolder() . "GlobalWarps.yml", Config::YAML);
        $warps = $config->get("warps", []);

        return isset($warps[$warpName]);
    }

    private function getWarpLocation(string $warpName): ?array {
        $config = new Config($this->plugin->getDataFolder() . "GlobalWarps.yml", Config::YAML);
        $warps = $config->get("warps", []);

        return $warps[$warpName] ?? null;
    }

    private function teleportToWarp(Player $player, array $warpLocation): void {
        $world = $warpLocation["world"];
        $x = $warpLocation["x"];
        $y = $warpLocation["y"];
        $z = $warpLocation["z"];
        $yaw = $warpLocation["yaw"];
        $pitch = $warpLocation["pitch"];

        $player->teleport($this->plugin->getServer()->getLevelByName($world)->getSpawnLocation()->setComponents($x, $y, $z), $yaw, $pitch);
    }
}
