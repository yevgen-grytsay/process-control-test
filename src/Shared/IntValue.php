<?php
namespace YevgenGrytsay\ProcessControl\Shared;

/**
 * @author: yevgen
 * @date: 02.04.17
 */
class IntValue implements Value
{
    /**
     * @var int
     */
    private static $size;
    /**
     * @var Value
     */
    private $value;

    public static function getSize()
    {
        //todo: signed not supported
        return strlen(PHP_INT_MAX.'');
    }

    /**
     * SharedInt constructor.
     * @param Value $value
     */
    public function __construct(Value $value)
    {
        $this->value = $value;
        static::$size = self::getSize();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function get()
    {
        return $this->decode($this->value->get());
    }

    /**
     * @param mixed $value
     * @throws \Exception
     */
    public function set($value)
    {
        $this->value->set($this->encode($value));
    }

    public function inc()
    {
        $this->set($this->get() + 1);
    }

    /**
     * @return int
     */
    public function getInt()
    {
        return (int) $this->get();
    }

    /**
     * @param string $value
     * @return mixed
     */
    protected function encode($value)
    {
        $value = (int) $value;
        $value = (string) $value;

        if (strlen($value) > static::$size) {
            //todo: probably signed int
        }

        $padSize = static::$size - strlen($value);
        return str_repeat('0', $padSize).$value;
    }

    /**
     * @param string $value
     * @return mixed
     */
    protected function decode($value)
    {
        return (int) $value;
    }
}