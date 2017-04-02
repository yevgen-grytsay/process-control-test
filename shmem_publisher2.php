<?php
/**
 * @author: yevgen
 * @date: 02.04.17
 */
if ($argc < 3) {
    die('Usage: '. basename(__FILE__).' <filename>');
}

$shm_key = $argv[1];
$char = $argv[2][0];
$shm_id = shmop_open($shm_key, 'c', 0666, 1);
if ($shm_id === false) {
    throw new Exception("Couldn't create shared memory block");
}

if (shmop_write($shm_id, $char, 0) !== false) {
    echo 'OK';
} else {
    echo 'Error';
}
shmop_close($shm_id);
echo PHP_EOL;
