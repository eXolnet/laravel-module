<?php

if (! function_exists('secureString')) {
    /**
     * @param string $value
     * @return \Exolnet\Support\SecureString
     */
    function secureString($value)
    {
        return new \Exolnet\Support\SecureString($value);
    }
}
