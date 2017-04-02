<?php
/**
 * @author: yevgen
 * @date: 02.04.17
 */
$tmp = tempnam('/tmp', 'PHP');
$shm_key = ftok($tmp, 'a');
$shm_id = shmop_open($shm_key, 'c', 0666, 1);
if ($shm_id === false) {
    throw new Exception("Couldn't create shared memory block");
}
echo 'Shared memory key: ', $shm_key, PHP_EOL;

do {
    $data = shmop_read($shm_id, 0, 1);
    echo $data, PHP_EOL;
    sleep(1);
} while ($data !== '=');
shmop_delete($shm_id);