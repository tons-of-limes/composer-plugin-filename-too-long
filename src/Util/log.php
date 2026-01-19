<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>.
 */
declare(strict_types=1);

namespace TonsOfLimes\FilenameTooLongPlugin\Util;

const PLUGIN_NAME = 'tonsoflimes/plugin-filename-too-long';

/**
 * Format log message related to package processing.
 *
 * @param string $package
 * @param string $message
 * @param ...$args
 * @return string
 */
function format(string $package, string $message, ...$args): string {
    $parts = array_filter([
        PLUGIN_NAME,
        sprintf('[%s]', $package),
        ' ',
        sprintf($message, ...$args)
    ]);

    return implode('', $parts);
}
