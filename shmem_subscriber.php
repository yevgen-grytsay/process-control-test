<?php
/**
 * @author: yevgen
 * @date: 02.04.17
 */
if ($argc < 2) {
    die('Usage: '. basename(__FILE__).' <filename>');
}

$filename = $argv[1];
$shm_key = ftok($filename, 'a');
$shm_id = shmop_open($shm_key, 'c', 0666, 1);
if ($shm_id === false) {
    throw new Exception("Couldn't create shared memory block");
}

do {
    $data = shmop_read($shm_id, 0, 1);
    echo $data, PHP_EOL;
    sleep(1);
} while ($data !== '=');
//shmop_close($shm_id);
shmop_delete($shm_id);