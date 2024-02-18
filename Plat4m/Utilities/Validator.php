<?php

namespace Plat4m\Utilities;

use DateTime;

class Validator
{
    // Validation errors.
    private $errors = [];

    // Name to be used in error message.
    private $name = "";

    // String value.
    private $str = "";

    // Integer value.
    private $nInt = 0;

    // Float value.
    private $nFloat = 0.0;

    /**
     * Checks if there are any validation errors.
     * @return bool
     */
    public function anyErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Joins multiple validation error messages into a string.
     * @return string
     */
    public function errStr()
    {
        return join(" ", $this->errors);
    }

    /**
     * Sets name to be used in error message.
     * @param string $name Name.
     * @return object Self.
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets string value which is to be validated.
     * @param string $str String to be validated.
     * @return object Self.
     */
    public function str($str)
    {
        if (!is_string($str)) {
            $this->errors[] = "{$this->name} is not a string.";
        }

        $this->str = $str;
        return $this;
    }

    /**
     * Checks if string is empty.
     * @return object Self.
     */
    public function reqStr()
    {
        if ($this->str == "") {
            $this->errors[] = "{$this->name} required.";
        }

        return $this;
    }

    /**
     * Checks if string has minimum length.
     * @param int $len Length.
     * @return object Self.
     */
    public function minStr($len)
    {
        if (strlen($this->str) < $len) {
            $this->errors[] = "{$this->name} must have a minimum of {$len} characters.";
        }

        return $this;
    }

    /**
     * Checks if string exceeds maximum length.
     * @param int $len Length.
     * @return object Self.
     */
    public function maxStr($len)
    {
        if (strlen($this->str) > $len) {
            $this->errors[] = "{$this->name} must not be a maximum of {$len} characters.";
        }

        return $this;
    }

    /**
     * Checks if string is an email.
     * @return object Self.
     */
    public function strEmail()
    {
        if (!filter_var($this->str, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email `{$this->str}`.";
        }
    }

    /**
     * Sets an integer value to be validated..
     * @param int $num Number to be validated.
     * @return object Self.
     */
    public function nInt($num)
    {
        if (!is_int($num)) {
            $this->errors[] = "{$this->name} is not an integer.";
        }

        $this->nInt = $num;
        return $this;
    }

    /**
     * Checks if number is greater than passed vlaue.
     * @param int $min Minimum number.
     * @return object Self.
     */
    public function minInt($min)
    {
        if ($this->nInt < $min) {
            $this->errors[] = "{$this->name} must be greater than {$min}.";
        }

        return $this;
    }

    /**
     * Checks if number is less than passed vlaue.
     * @param int $max Maximum number.
     * @return object Self.
     */
    public function maxInt($max)
    {
        if ($this->nInt > $max) {
            $this->errors[] = "{$this->name} must be less than {$max}.";
        }

        return $this;
    }

    /**
     * Sets a float value to be validated.
     * @param float $num Value to be validated.
     * @return object Self.
     */
    public function nFloat($num)
    {
        if (!is_float($num)) {
            $this->errors[] = "{$this->name} is not a float.";
        }

        $this->nFloat = $num;
        return $this;
    }

    /**
     * Checks if number is greater than passed vlaue.
     * @param int $min Minimum number.
     * @return object Self.
     */
    public function minFloat($min)
    {
        if ($this->nFloat < $min) {
            $this->errors[] = "{$this->name} must be greater than {$min}.";
        }

        return $this;
    }

    /**
     * Checks if number is less than passed vlaue.
     * @param int $max Maximum number.
     * @return object Self.
     */
    public function maxFloat($max)
    {
        if ($this->nFloat > $max) {
            $this->errors[] = "{$this->name} must be less than {$max}.";
        }

        return $this;
    }

    /**
     * Checks if value matches the regexp.
     * @param mixed $val Value to be validated.
     * @param string $exp Regular expression.
     * @param string $msg Message to be returned as error.
     * @return object Self.
     */
    public function regexp($val, $exp, $msg = NULL)
    {
        if (!preg_match($exp, $val)) {
            if ($msg) {
                $this->errors[] = $msg;
            } else {
                $this->errors[] = "Invalid {$this->name}.";
            }
        }

        return $this;
    }

    /**
     * Checks if datetime is in required format.
     * @param string $format Format.
     * @return object Self.
     */
    public function formatDT($format = "Y-m-d H:i:s")
    {
        $d = DateTime::createFromFormat($format, $this->str);
        if (!($d && $d->format($format) == $this->str)) {
            $this->errors[] = "Invalid datetime format for {$this->str}. Expected {$format}.";
        }

        return $this;
    }
}
