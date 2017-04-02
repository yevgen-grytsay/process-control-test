<?php
namespace YevgenGrytsay\ProcessControl\Shared;

/**
 * @author: yevgen
 * @date: 02.04.17
 */
interface Value {
    /**
     * @return mixed
     * @throws \Exception
     */
    public function get();

    /**
     * @param mixed $value
     * @throws \Exception
     */
    public function set($value);
}