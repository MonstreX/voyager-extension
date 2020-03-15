<?php


namespace MonstreX\VoyagerExtension;

use MonstreX\VoyagerExtension\Contracts\VoyagerExtension as VoyagerExtensionContract;
use App;

class VoyagerExtension implements VoyagerExtensionContract
{

    /*
     * Get Translation from the given string using locale code
     * @param: {{en}}text in english{{ru}}text in russian
     * @return: text in current or given locale
     */
    public function trans(string $string, $lang = null)
    {
        if (!$lang) {
            $lang = App::getLocale();
        }
        foreach (explode('{{', $string) as $line) {
            if (substr($line,0,4) === $lang . '}}') {
                return substr($line,4, strlen($line) - 4);
            }
        }
        return $string;
    }


}