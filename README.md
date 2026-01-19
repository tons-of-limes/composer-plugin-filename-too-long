# Overview

The plugin allows to install composer packages,
which includes files with names longer than 143 characters in the encrypted directory.
An extension removes invalid files from downloaded package archive before installation.

#### Example of error during `Magento 2` installation which can be fixed by the plugin
```
Package operations: 1 install, 0 updates, 0 removals
  - Installing magento/magento2-base (2.4.8-p3): Extracting archive
    Failed to extract magento/magento2-base: (50) /usr/bin/unzip -qq /var/www/html/app/vendor/composer/tmp-68fa5d83f487173e3e0d08568bd8d9ad.zip -d /var/www/html/app/vendor/composer/da6edea3
                                                                                                                                                                                                                           
error:  cannot create /var/www/html/app/vendor/composer/da6edea3/dev/tests/acceptance/tests/_data/adobe-base-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name.jpg                                                                                                                                                                                      
        File name too long                                                                                                                                                                                                 
                                                                                                                                                                                                                           
    The archive may contain identical file names with different capitalization (which fails on case insensitive filesystems)
    Unzip with unzip command failed, falling back to ZipArchive class
    Install of magento/magento2-base failed

In ZipDownloader.php line 253:
                                                                                                                                                                                                                          
  The archive may contain identical file names with different capitalization (which fails on case insensitive filesystems): ZipArchive::extractTo(/var/www/html/app/vendor/composer/da6edea3/dev/tests/acceptance/tests/  
  _data/adobe-base-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name.jpg): Failed to open stream: File name too long        
                                                                                                                                                                                                                          

In ZipDownloader.php line 240:
                                                                                                                                                                                                                          
  ZipArchive::extractTo(/var/www/html/app/vendor/composer/da6edea3/dev/tests/acceptance/tests/_data/adobe-base-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name-image-long-name-image-lon  
  g-name-image-long-name-image-long-name.jpg): Failed to open stream: File name too long    
  
```

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
