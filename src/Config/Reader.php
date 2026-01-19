<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>.
 */
declare(strict_types=1);

namespace TonsOfLimes\FilenameTooLongPlugin\Config;

use Composer\Composer;

/**
 * Config reader.
 */
readonly class Reader
{
    private const EXTRA_NAMESPACE = 'tonsoflimes/plugin-filename-too-long';
    private const EXTRA_ALLOW_KEY = 'allow';

    /**
     * Read config from global Composer config.
     *
     * @param Composer $composer
     * @return Config
     */
    public function read(Composer $composer): Config
    {
        $globalConfig = $this->readGlobalComposerConfigAsArray($composer);

        if ($globalConfig === null) {
            return new Config([]); // default config
        }

        $allowedPackages = $this->getAllowedPackagesFromGlobalConfig($globalConfig);

        return new Config($allowedPackages);
    }

    /**
     * Read and decode global Composer config (~/.composer/config.json) into an associative array.
     *
     * Returns a null if the config is missing or invalid.
     *
     * @param Composer $composer
     * @return array|null
     */
    private function readGlobalComposerConfigAsArray(Composer $composer): array|null
    {
        $config = $composer->getConfig();

        $homeDir = $config->get('home');
        if (!is_string($homeDir) || $homeDir === '') {
            return null;
        }

        $globalConfigPath = rtrim($homeDir, '/').'/composer.json';
        if (!is_file($globalConfigPath) || !is_readable($globalConfigPath)) {
            return null;
        }

        $json = file_get_contents($globalConfigPath);
        if ($json === false) {
            return null;
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            return null;
        }

        return $data;
    }

    /**
     * Get allowed packages from global Composer config array.
     *
     * @return string[]
     */
    private function getAllowedPackagesFromGlobalConfig(array $globalConfig): array
    {
        $allowedPackagesConfig = $globalConfig['extra'][self::EXTRA_NAMESPACE][self::EXTRA_ALLOW_KEY] ?? [];
        $allowedPackagesConfig = is_array($allowedPackagesConfig) ? $allowedPackagesConfig : [];

        $allowedPackages = [];
        foreach ($allowedPackagesConfig as $packageName => $isAllowed) {
            if (is_string($packageName) && $isAllowed === true) {
                $allowedPackages[] = $packageName;
            }
        }

        return $allowedPackages;
    }
}
