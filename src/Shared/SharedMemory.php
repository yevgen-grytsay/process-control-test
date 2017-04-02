<?php
namespace YevgenGrytsay\ProcessControl\Shared;

/**
 * @author: yevgen
 * @date: 02.04.17
 */
class SharedMemory
{
    private $size;
    private $chunks = [];
    private $offset = 0;
    private $id;

    /**
     * SharedMemory constructor.
     */
    public function __construct($size)
    {
        $this->size = $size;

        $shmKey = ftok(__FILE__, 'a');
//        $tmp = tempnam('/tmp', 'PHP');
//        $shmKey = ftok($tmp, 'a');
        $this->id = shmop_open($shmKey, 'c', 0666, $this->size);
        if ($this->id === false) {
            throw new \Exception("Couldn't create shared memory block");
        }
    }

    public function createChunk($size)
    {
        if ($this->getFreeSize() < $size) {
            throw new \Exception("Couldn't create memory chunk. Not enough memory.");
        }
        $chunk = new SharedMemoryChunk($this->id, $size, $this->offset);
        $chunks[] = $chunk;
        $this->offset += $size;
        return $chunk;
    }

    private function getFreeSize()
    {
        return $this->size - $this->offset - 1;
    }
}