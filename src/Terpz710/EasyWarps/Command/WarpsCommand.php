<?php

declare(strict_types=1);

namespace Terpz710\EasyWarps\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use Terpz710\EasyWarps\Main;

class WarpsCommand extends Command {

    private $plugin;

    public function __construct(Plugin $plugin) {
        parent::__construct("warps", "List available warps", "/warps");
        $this->plugin = $plugin;
        $this->setPermission("easywarp.warps");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof CommandSender) {
            $warps = $this->getWarpsList();

            if (!empty($warps)) {
                $sender->sendMessage(TextFormat::YELLOW . "Available warps:");
                $sender->sendMessage(TextFormat::YELLOW . implode(", ", $warps));
            } else {
                $sender->sendMessage(TextFormat::RED . "No warps are available.");
            }

            return true;
        }
    }

    private function getWarpsList(): array {
        $config = new Config($this->plugin->getDataFolder() . "EasyWarps.yml", Config::YAML);
        $warps = $config->get("warps", []);

        return array_keys($warps);
    }
}
