<?php
namespace YevgenGrytsay\ProcessControl\Shared;

/**
 * @author: yevgen
 * @date: 02.04.17
 */
class MemoryChunk implements Value
{
    private $offset;
    private $size;
    private $id;

    /**
     * SharedMemoryChunk constructor.
     * @param $size
     * @param $offset
     */
    public function __construct($id, $size, $offset)
    {
        $this->offset = $offset;
        $this->size = $size;
        $this->id = $id;
    }

    /**
     * @throws \Exception
     * @return mixed
     */
    public function get()
    {
        $data = shmop_read($this->id, $this->offset, $this->size);
        if ($data === false) {
            throw new \Exception("Couldn't read from shared memory");
        }
        return $data;
    }

    /**
     * @param $value
     * @throws \Exception
     */
    public function set($value)
    {
        if (shmop_write($this->id, $value, $this->offset) === false) {
            throw new \Exception("Couldn't write to shared memory");
        }
    }

}