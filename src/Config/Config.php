<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>.
 */
declare(strict_types=1);

namespace TonsOfLimes\FilenameTooLongPlugin\Config;

/**
 * Config model.
 */
readonly class Config
{
    /**
     * @param string[] $allowedPackages
     */
    public function __construct(private array $allowedPackages = [])
    {}

    /**
     * Get list of packages allowed for plugin processing.
     *
     * @return string[]
     */
    public function getAllowedPackages(): array
    {
        return $this->allowedPackages;
    }
}
