<?php
/**
 * @author: yevgen
 * @date: 02.04.17
 */
use YevgenGrytsay\ProcessControl\Shared\SharedInt;
use YevgenGrytsay\ProcessControl\Shared\SharedMemory;
use YevgenGrytsay\ProcessControl\Shared\SyncSharedValue;

require_once __DIR__.'/vendor/autoload.php';

/**
 * @param $initialValue
 * @return SharedInt
 */
function createSyncedInt($initialValue)
{
    $mem = new SharedMemory(100);
    $intChunk = $mem->createChunk(SharedInt::getSize());
    $syncInt = new SyncSharedValue(sem_get(ftok('.', '.')), $intChunk);
    $int = new SharedInt($syncInt);
    $int->set($initialValue);
    return $int;
}


ob_start();
ob_end_flush();
$int = createSyncedInt(0);

$pid = pcntl_fork();
if ($pid == 0) {
    $counter = 0;
    $values = [];
    foreach (range(1, 100) as $value) {
        $values[] = $int->inc();
        usleep(1000);
    }
//    echo 'child',PHP_EOL;
//    var_dump($values);
    exit(0);
}

$values = [];
$prev = -1;
do {
    $counter = $int->get();
    if ($counter != $prev) {
        $values[] = $counter;
        $prev = $counter;
    }
//    echo 'parent: ', $mem->get(), PHP_EOL;
//    usleep(5000);
} while ($counter < 100);
var_dump($values);