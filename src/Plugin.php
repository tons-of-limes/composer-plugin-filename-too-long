<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>.
 */
declare(strict_types=1);

namespace TonsOfLimes\FilenameTooLongPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PostFileDownloadEvent;
use TonsOfLimes\FilenameTooLongPlugin\Config\Config;
use TonsOfLimes\FilenameTooLongPlugin\Config\Reader;
use TonsOfLimes\FilenameTooLongPlugin\Hook\OnPostFileDownload;

/**
 * Plugin definition.
 *
 * The plugin intercepts via `\Composer\Plugin\PluginEvents::POST_FILE_DOWNLOAD` events
 * to exclude files with names that exceed the limit from the downloaded archive.
 */
class Plugin implements PluginInterface, EventSubscriberInterface
{
    private ?Composer $composer;
    private ?IOInterface $io;
    private ?Config $config;

    /**
     * @inheritDoc
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->config = (new Reader())->read($composer);
    }

    /**
     * @inheritDoc
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {}

    /**
     * @inheritDoc
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {}

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PluginEvents::POST_FILE_DOWNLOAD => 'onPostFileDownload',
        ];
    }

    /**
     * Listener on `post-file-download` event.
     *
     * @param PostFileDownloadEvent $event
     * @return void
     */
    public function onPostFileDownload(PostFileDownloadEvent $event): void
    {
        if (!$this->io || !$this->composer || !$this->config) {
            return;
        }

        (new OnPostFileDownload($this->composer, $this->io, $this->config))
            ->execute($event);
    }
}
