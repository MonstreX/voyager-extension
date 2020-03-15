<?php
namespace MonstreX\VoyagerExtension\Contracts;

interface VoyagerExtension
{
    /*
     * Get Translation from the given string using locale code
     */
    public function trans(string $string, $lang = null);
}
