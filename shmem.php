<?php
/**
 * @author: yevgen
 * @date: 02.04.17
 */
$mem = new TestSharedInt();
ob_start();
ob_end_flush();
$pid = pcntl_fork();
if ($pid == 0) {
    $counter = 0;
    $values = [];
    foreach (range(1, 100) as $value) {
        $values[] = $mem->inc();
//        usleep(10000);
    }
//    echo 'child', PHP_EOL;
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
//    usleep(1000);
} while ($counter < 100);
var_dump($values);
$counter = $mem->get();
var_dump($counter);

/**
 * @link http://php.net/manual/en/function.shm-attach.php
 * Class SharedMemory
 */
class TestSharedInt {
    private $id;

    public function __construct($value = 0)
    {
        //todo: signed not supported
        $max = PHP_INT_MAX.'';
        $this->size = strlen($max);
        $shm_key = ftok(__FILE__, 'a');
//        $tmp = tempnam('/tmp', 'PHP');
//        $shm_key = ftok($tmp, 'a');
        $this->id = shmop_open($shm_key, 'c', 0666, $this->size);
        if ($this->id === false) {
            throw new Exception("Couldn't create shared memory block");
        }
        $this->set($value);
    }

    public function inc()
    {
        return $this->set($this->get() + 1);
    }

    /**
     * @throws Exception
     */
    public function get()
    {
        $data = shmop_read($this->id, 0, $this->size);
        if ($data === false) {
            throw new Exception("Couldn't read from shared memory");
        }
        return (int) $data;
    }

    public function set($value)
    {
        $value = (int) $value;
        $value = (string) $value;

        $padSize = $this->size - strlen($value);
        $value = str_repeat('0', $padSize).$value;

        shmop_write($this->id, $value, 0);
        return $value;
    }
}