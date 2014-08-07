<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Symfony\Component\Finder\Finder;
use Mathielen\ImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use Mathielen\ImportEngine\Storage\Format\Discovery\MimeTypeDiscoverStrategy;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

class FinderFileStorageProvider extends AbstractFileStorageProvider implements \IteratorAggregate
{

    /**
     * @var Finder
     */
    private $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
        $this->setStorageFactory(
            new DefaultLocalFileStorageFactory(
                new MimeTypeDiscoverStrategy()));
    }

    public function getIterator()
    {
        $files = array();
        foreach ($this->finder->files() as $file) {
            $item = new StorageSelection(new \SplFileInfo($file), $file->getFilename(), $file->getFilename());
            $files[] = $item;
        }

        return new \ArrayIterator($files);
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::select()
     */
    public function select($id = null)
    {
        $selection = $id;
        if (is_string($id)) {
            if (!file_exists($id)) {
                throw new \InvalidArgumentException("id is not a valid file path: ".$id);
            }
            $selection = new StorageSelection(new \SplFileInfo($id), $id, $id);

            return $selection;
        }

        return parent::select($id);
    }

}
