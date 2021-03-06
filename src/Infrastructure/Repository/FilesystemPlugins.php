<?php

namespace FP\Larmo\Infrastructure\Repository;

use FP\Larmo\Domain\Service\PluginsCollection;
use FP\Larmo\Domain\Repository\Plugins as PluginsRepository;

/**
 * Class PluginsFilesystemAdapter
 *
 * Simple implementation of PluginsAdapterInterface
 * for filesystem based plugins system.
 *
 * Requires all plugins to reside in FP\Larmo\Plugin namespace.
 * Plugin directory name should be lowercase and first letter
 * should be capitalized. Each plugin should have PluginManifest
 * class that will implement PluginManifestInterface.
 *
 * @package FP\Larmo\Infrastructure\Adapter
 */
class FilesystemPlugins implements PluginsRepository
{
    /**
     * @var \DirectoryIterator
     */
    private $iterator;

    /**
     * @param \DirectoryIterator $iterator
     */
    public function __construct(\DirectoryIterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * @param PluginsCollection $plugins
     */
    public function retrieve(PluginsCollection $plugins)
    {
        $namespace = '\\FP\\Larmo\\Plugin\\';
        $pluginManifest = '\\PluginManifest';

        foreach ($this->iterator as $fileInfo) {
            if (!$fileInfo->isDot() && $fileInfo->isDir()) {
                $pluginName = $fileInfo->getFileName();
                $pluginClass = $namespace . $pluginName . $pluginManifest;

                if (class_exists($pluginClass)) {
                    $plugin = new $pluginClass();
                    $plugins[] = $plugin;
                }
            }
        }
    }
}