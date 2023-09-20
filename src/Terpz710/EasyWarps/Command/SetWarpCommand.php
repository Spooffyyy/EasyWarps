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

class SetWarpCommand extends Command {

    private $plugin;

    public function __construct(Plugin $plugin) {
        parent::__construct("setwarp", "Set a warp point in your current world", "/setwarp <warpName>");
        $this->plugin = $plugin;
        $this->setPermission("easywarp.setwarp");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (count($args) === 1) {
                $warpName = strtolower($args[0]);

                // Generate a permission node based on the warp name
                $warpPermission = "easywarp.setwarp.$warpName";

                if ($sender->hasPermission($warpPermission)) {
                    $position = $sender->getPosition(); // Get the player's position.

                    $warpLocation = [
                        'x' => $position->getX(),
                        'y' => $position->getY(),
                        'z' => $position->getZ(),
                        'world' => $sender->getWorld()->getFolderName(),
                        'permission' => $warpPermission,
                    ];

                    $this->saveGlobalWarpData($warpName, $warpLocation);

                    $sender->sendMessage(TextFormat::GREEN . "Global warp point '$warpName' set!");
                    return true;
                } else {
                    $sender->sendMessage(TextFormat::RED . "You do not have permission to set this warp.");
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "Usage: /setwarp <warpName>");
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "You must be a player to use this command.");
        }
        return false;
    }

    private function saveGlobalWarpData(string $warpName, array $warpLocation): void {
        $config = new Config($this->plugin->getDataFolder() . "GlobalWarps.yml", Config::YAML);

        if (!$config->exists("warps")) {
            $config->set("warps", []);
        }

        $warps = $config->get("warps");
        $warps[$warpName] = $warpLocation;

        $config->set("warps", $warps);
        $config->save();
    }
}
