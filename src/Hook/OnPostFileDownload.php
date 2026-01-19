<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>.
 */
declare(strict_types=1);

namespace TonsOfLimes\FilenameTooLongPlugin\Hook;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\CompletePackageInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PostFileDownloadEvent;
use TonsOfLimes\FilenameTooLongPlugin\Config\Config;
use ZipArchive;
use function TonsOfLimes\FilenameTooLongPlugin\Util\format;

/**
 * On `post-file-download` hook.
 */
class OnPostFileDownload
{
    private const MAX_FILENAME_LENGTH = 143;

    public function __construct(
        private readonly Composer $composer,
        private readonly IOInterface $io,
        private readonly Config $config,
    ) {}

    /**
     * Handle event.
     *
     * @param PostFileDownloadEvent $event
     * @return void
     */
    public function execute(PostFileDownloadEvent $event): void
    {
        $packageArchiveFilename = $event->getFileName();
        if (!$this->isValidFilename($packageArchiveFilename)) {
            return;
        }

        $context = $event->getContext();
        if (!$context instanceof CompletePackageInterface) {
            return;
        }

        if (!$this->isAllowedByPackage($context)) {
            return;
        }

        $this->io->info(format($context->getName(), 'Processing archive...'));

        $archive = new ZipArchive();
        if ($archive->open($packageArchiveFilename) !== true) {
            $this->io->error(
                format($context->getName(), 'Can\'t open archive file %s', $packageArchiveFilename)
            );
            return;
        }

        $fileList = $this->getArchiveFilesList($archive);
        $this->io->debug(
            format($context->getName(), 'Found %d files in the archive', count($fileList))
        );

        $invalidFileList = array_filter($fileList, function (string $file) {
            return !$this->isPackageFileNameLengthValid($file);
        });
        $this->io->debug(
            format($context->getName(), 'Found %d invalid files in the archive', count($invalidFileList))
        );

        $processedCount = 0;
        foreach ($invalidFileList as $file) {
            $isSuccess = $this->processFile($context, $archive, $file);

            $processedCount += (int)$isSuccess;
        }

        if (!$processedCount) {
            $this->io->info(
                format($context->getName(), 'Processed archive. No fixes.')
            );

            return;
        }

        $isClosed = $archive->close();

        if (!$isClosed) {
            $this->io->error(
                format($context->getName(), 'Can\'t save changes to archive file')
            );

            return;
        }

        $this->io->info(
            format($context->getName(), 'Processed archive. %d file(s) fixed.', $processedCount)
        );
    }

    /**
     * Validate is processing allowed for package.
     *
     * @param PackageInterface $package
     * @return bool
     */
    private function isAllowedByPackage(PackageInterface $package): bool
    {
        return in_array($package->getName(), $this->config->getAllowedPackages());
    }

    /**
     * Validate filename of downloaded package.
     *
     * @param mixed $filename
     * @return bool
     */
    private function isValidFilename($filename): bool
    {
        if (!is_string($filename)) {
            return false;
        }

        return str_ends_with($filename, '.zip');
    }

    /**
     * Validate length of package filename.
     *
     * @param $filename
     * @return bool
     */
    private function isPackageFileNameLengthValid($filename): bool
    {
        $basename = basename($filename);

        return strlen($basename) <= self::MAX_FILENAME_LENGTH;
    }

    /**
     * Get list of files in the archive.
     *
     * @param ZipArchive $archive
     * @return string[]
     */
    private function getArchiveFilesList(ZipArchive $archive): array
    {
        $fileList = [];

        for ($i = 0; $i < $archive->numFiles; $i++) {
            $stat = $archive->statIndex($i);
            if ($stat === false) {
                continue;
            }

            $name = $stat['name'] ?? null;

            if (!is_string($name)) {
                continue;
            }

            $fileList[] = $name;
        }

        return $fileList;
    }

    /**
     * Process file in the archive.
     *
     * @param PackageInterface $package
     * @param ZipArchive $archive
     * @param string $filename
     * @return bool
     */
    private function processFile(PackageInterface $package, ZipArchive $archive, string $filename): bool
    {
        $this->io->debug(format($package->getName(), 'Processing file `%s`', $filename));

        $isDeleted = $archive->deleteName($filename);

        if (!$isDeleted) {
            $this->io->error(format($package->getName(), 'Can\'t delete file `%s`', $filename));
        }

        return $isDeleted;
    }
}
