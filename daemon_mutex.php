<?php
/**
 * @link https://github.com/php/php-src/blob/PHP-5.6.30/ext/sysvsem/sysvsem.c
 * @link https://adobkin.com/2011/12/04/1027/
 *
 * @author: yevgen
 * @date: 08.04.17
 */
$ipcKey = ftok(__FILE__, 'a');
var_dump($ipcKey);
$sem = sem_get($ipcKey);
if (!sem_acquire($sem, true)) {
    die("Couldn't acquire lock");
}
$i = 0;
while (true) {
    if ($i === 1) {
        eval('{');
    }
    echo 'Working...', PHP_EOL;
    sleep(5);
    ++$i;
}