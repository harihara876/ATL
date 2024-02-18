<?php

namespace Plat4m\Utilities;

class OTP
{
    /**
     * Generate OTP.
     * @param int $n Number of digits.
     * @return int OTP.
     */
    public static function generate($n)
    {
        $generator = "1357902468";
        $result = "";

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand() % (strlen($generator))), 1);
        }

        return $result;
    }
}
