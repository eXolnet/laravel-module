<?php

namespace Exolnet\Support;

class SecureString
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'value' => str_repeat('*', strlen($this->value)),
        ];
    }
}
