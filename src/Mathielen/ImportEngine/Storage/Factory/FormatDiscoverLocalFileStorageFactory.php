<?php

namespace Mathielen\ImportEngine\Storage\Factory;

use Mathielen\ImportEngine\Storage\LocalFileStorage;
use Mathielen\ImportEngine\Storage\Format\Discovery\FormatDiscoverStrategyInterface;
use Mathielen\ImportEngine\ValueObject\StorageSelection;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;
use Psr\Log\LoggerInterface;

class FormatDiscoverLocalFileStorageFactory implements StorageFactoryInterface
{
    /**
     * @var FormatDiscoverStrategyInterface
     */
    private $formatDiscoverStrategyInterface;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(FormatDiscoverStrategyInterface $formatDiscoverStrategyInterface, LoggerInterface $logger = null)
    {
        $this->formatDiscoverStrategyInterface = $formatDiscoverStrategyInterface;
        $this->logger = $logger;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Storage\Factory\StorageFactoryInterface::factor()
     */
    public function factor(StorageSelection $selection)
    {
        $file = $selection->getImpl();

        if (!($file instanceof \SplFileInfo)) {
            throw new InvalidConfigurationException('StorageSelection does not contain a SplFileInfo as impl property but this is mandatory for a LocalFileStorage.');
        }
        if (!$file->isFile() || !$file->isReadable()) {
            throw new InvalidConfigurationException('StorageSelection references a File that does not exists or is not readable.');
        }

        $format = $selection->getMetadata('format');
        if (!$format) {
            $format = $this->formatDiscoverStrategyInterface->getFormat($selection);
            if (!$format) {
                throw new InvalidConfigurationException('Could not discover format!');
            }

            if ($this->logger) {
                $this->logger->info("File $file was discovered as format '$format'", ['selection' => $selection->toArray()]);
            }
        }

        $localFile = new LocalFileStorage($file, $format);

        return $localFile;
    }
}
