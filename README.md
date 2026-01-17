# Overview

The plugin allows to install composer packages,
which includes files with names longer than 143 characters in the encrypted directory.

# Installation

## 1. Install plugin globally
```
composer global require tonsoflimes/plugin-filename-too-long
```

## 2. Allow plugin globally
```
composer global config --no-plugins allow-plugins.tonsoflimes/plugin-filename-too-long true
```

## 3. Enable plugin for specific package
Plugin is not enabled by default for any package.
You need to explicitly allow it for each package you want to process.
```
composer global config --json --merge extra.tonsoflimes/plugin-filename-too-long.allow '{"%package/name%": true}'
```

### Examples
#### A. Allow `magento/magento2-base` package processing
```
composer global config --json --merge extra.tonsoflimes/plugin-filename-too-long.allow '{"magento/magento2-base": true}'
```
