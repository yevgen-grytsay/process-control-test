<?php
/**
 * @author: yevgen
 * @date: 02.04.17
 */
$counter = 0;
ob_start();
ob_end_flush();
$pid = pcntl_fork();
if ($pid == 0) {
    foreach (range(1, 100) as $value) {
        $counter += 1;
        echo 'child: ', $counter, PHP_EOL;
    }
    exit(0);
}

foreach (range(1, 100) as $value) {
    $counter += 1;
    echo 'parent: ', $counter, PHP_EOL;
}

// an extension needs to be installed in order to use setproctitle
//setproctitle();