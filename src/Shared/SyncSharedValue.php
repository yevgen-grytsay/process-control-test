<?php
namespace YevgenGrytsay\ProcessControl\Shared;

/**
 * @author: yevgen
 * @date: 02.04.17
 */
class SyncSharedValue implements ISharedValue
{
    /**
     * @var resource
     */
    private $semId;
    /**
     * @var ISharedValue
     */
    private $val;

    /**
     * SyncSharedValue constructor.
     * @param $semId
     * @param ISharedValue $val
     */
    public function __construct($semId, ISharedValue $val)
    {
        $this->semId = $semId;
        $this->val = $val;
    }

    public function get()
    {
        $this->lock();
        $res = $this->val->get();
        $this->unlock();
        return $res;
    }

    public function set($value)
    {
        $this->lock();
        $this->val->set($value);
        $this->unlock();
    }

    private function lock()
    {
        if (!sem_acquire($this->semId)) {
            throw new \Exception("Couldn't acquire semaphore");
        }
    }

    private function unlock()
    {
        if (!sem_release($this->semId)) {
            throw new \Exception("Couldn't release semaphore");
        }
    }
}