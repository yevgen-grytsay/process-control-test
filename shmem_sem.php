<?php
/**
 * @author: yevgen
 * @date: 02.04.17
 */
$mem = new Test2SyncSharedInt(new Test2SharedInt(), sem_get(ftok('.', '.')));
ob_start();
ob_end_flush();

$pid = pcntl_fork();
if ($pid == 0) {
    $counter = 0;
    $values = [];
    foreach (range(1, 100) as $value) {
        $values[] = $mem->inc();
        usleep(1000);
    }
//    echo 'child',PHP_EOL;
//    var_dump($values);
    exit(0);
}

$values = [];
$prev = -1;
do {
    $counter = $mem->get();
    if ($counter != $prev) {
        $values[] = $counter;
        $prev = $counter;
    }
//    echo 'parent: ', $mem->get(), PHP_EOL;
//    usleep(5000);
} while ($counter < 100);
var_dump($values);

/**
 * @link http://php.net/manual/en/function.shm-attach.php
 * Class SharedMemory
 */
class Test2SharedInt {
    private $id;

    private $offset = 0;

    public function __construct($value = 0)
    {
//        todo: this leads to memory corruption
//        todo: it seems that without semaphore a variable is cached
//        $shm_key = ftok(__FILE__, 'a');
        $tmp = tempnam('/tmp', 'PHP');
        $shm_key = ftok($tmp, 'a');
        $this->id = shmop_open($shm_key, 'c', 0666, 100);
        if ($this->id === false) {
            throw new Exception("Couldn't create shared memory block");
        }
        $this->set($value);
    }

    public function inc()
    {
        $data = $this->get();
        return $this->set($data + 1);
    }

    /**
     * @throws Exception
     */
    public function get()
    {
        $data = shmop_read($this->id, $this->offset, 4);
        if ($data === false) {
            throw new Exception("Couldn't read from shared memory");
        }
        return (int) $data;
    }

    public function set($value)
    {
        $value = (int) $value;
        $value = (string) $value;
        shmop_write($this->id, $value, $this->offset);
        return $value;
    }
}

class Test2SyncSharedInt extends Test2SharedInt
{
    /**
     * @var Test2SharedInt
     */
    private $int;
    /**
     * @var
     */
    private $semId;

    /**
     * Test2SyncSharedInt constructor.
     * @param Test2SharedInt $int
     */
    public function __construct(Test2SharedInt $int, $semId)
    {
        $this->int = $int;
        $this->semId = $semId;
    }

    public function inc()
    {
        $this->lock();
        $res = $this->int->inc();
        $this->unlock();
        return $res;
    }

    public function get()
    {
        $this->lock();
        $res = $this->int->get();
        $this->unlock();
        return $res;
    }

    public function set($value)
    {
        $this->lock();
        $res = $this->int->set($value);
        $this->unlock();
        return $res;
    }

    private function lock()
    {
        if (!sem_acquire($this->semId)) {
            throw new Exception("Couldn't acquire semaphore");
        }
    }

    private function unlock()
    {
        if (!sem_release($this->semId)) {
            throw new Exception("Couldn't release semaphore");
        }
    }
}