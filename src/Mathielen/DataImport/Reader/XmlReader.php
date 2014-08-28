<?php
namespace Mathielen\DataImport\Reader;

use Ddeboer\DataImport\Reader\ReaderInterface;

/**
 * Reads data from a xml file
 */
class XmlReader implements ReaderInterface
{

    /**
     * @var \Iterator
     */
    protected $iterableResult;

    private $filename;
    private $xpath;

    public function __construct(\SplFileObject $file, $xpath=null)
    {
        $this->filename = $file->getPathname();

        if (!is_null($xpath) && !is_string($xpath)) {
            throw new \InvalidArgumentException("xpath must be null or a string");
        } elseif (is_null($xpath)) {
            $xpath = '';
        }

        $this->xpath = $xpath;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return array_keys($this->current()); //TODO
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        if (!$this->iterableResult) {
            $this->rewind();
        }

        return (array) $this->iterableResult->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterableResult->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->iterableResult->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->iterableResult->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        if (!$this->iterableResult) {
            $this->iterableResult = new \SimpleXMLIterator(file_get_contents($this->filename));
        }

        $this->iterableResult->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if (!$this->iterableResult) {
            $this->rewind();
        }

        return count($this->iterableResult);
    }

}
