<?php

namespace Plat4mAPI\Util;

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

    // Number. Int or float.
    private $num = 0;

    /**
     * Append error message to errors array.
     * @param string $err Error.
     */
    public function appendErr($err)
    {
        $this->errors[] = $err;
    }

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
        if ($this->name === 'Quantity') {

            if (!is_int($num) && empty($num)) {

                $this->errors[] = "{$this->name} is not an integer. Please enter {$this->name}";
            } else if (!is_int($num)) {

                $this->errors[] = "{$this->name} is not an integer.";
            } else if ($num <= 0) {

                $this->errors[] = "{$this->name} must be grater than '0'.";
            }
        } else if (!is_int($num)) {

            if (!is_int($num) && !empty($num)) {
                $this->errors[] = "{$this->name} is not an integer.";
            }

            if (!is_int($num) && empty($num)) {
                $this->errors[] = "{$this->name} is not an integer. Please enter {$this->name}";
            }

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
        if (!is_float($num) && !empty($num)) {
            $this->errors[] = "{$this->name} is not a float.";
        } 
        if (!is_float($num) && empty($num)) {
            $this->errors[] = "{$this->name} is not a float. Please enter {$this->name}";
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
     * Checks if number is less than passed vlaue.
     * @param int $max Maximum number.
     * @return object Self.
     */
    public function num($num)
    {
        $isNum = is_int($num) || is_float($num);

        if (!$isNum) {
            $this->errors[] = "{$this->name} must be a number.";
        }

        $this->num = $num;
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
     * Checks if value matches the regexp.
     * @param mixed $val Value to be validated.
     * @return object Self.
     */
    public function regularexp($val){
        if (!preg_match('@[A-Z]@', $val)) {
            $this->errors[] = "{$this->name} must include at least one Capital letter.";
        }
        if (!preg_match('@[a-z]@', $val)) {
            $this->errors[] = "{$this->name} must include at least one lowercase.";
        }
        if (!preg_match('@[0-9]@', $val)) {
            $this->errors[] = "{$this->name} must include one number.";
        }
        if (!preg_match('@[^\w]@', $val)) {
            $this->errors[] = "{$this->name} must include at least one symbol.";
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
            $this->errors[] = "Invalid datetime format for {$this->name}. Expected {$format}.";
        }

        return $this;
    }

    /**
     * Checks if timezone is valid.
     * @return object Self.
     */
    public function validTZ()
    {
        if ($this->str) {
           if (!in_array($this->str, timezone_identifiers_list())) {
                $this->errors[] = "Invalid {$this->name}.";
            }
        } else{
            $this->errors[] = "Please enter {$this->name}.";
        }
        return $this;
    }

    public function upperCase($val)
    {
        if (!preg_match('@[A-Z]@', $val)) {
            $this->errors[] = "{$this->name} must include at least one Capital letter.";
        }
        return $this;
    }

    public function oneNum($val)
    {
        if (!preg_match('@[0-9]@', $val)) {
            $this->errors[] = "{$this->name} must include one number.";
        }
        return $this;
    }

    public function oneSymbol($val)
    {
        if (!preg_match('@[^\w]@', $val)) {
            $this->errors[] = "{$this->name} must include at least one symbol.";
        }
        return $this;
    }

    /**
     * Check if fromdate is less than todate required.
     * @param string $fromdate Format.
     * @param string $todate Format.
     * @return object Self.
     */
    public function compareDT($fromDate, $toDate)
    {
        if ($fromDate && $toDate) {
            $input["fromDate"] = convertDateTimeFormat($fromDate, "m/d/Y", "Y-m-d");
            $input["toDate"] = convertDateTimeFormat($toDate, "m/d/Y", "Y-m-d");
            if($input["fromDate"] > $input["toDate"]){
               $this->errors[] = "From date should not be greater than To date";
            }
            if ($input["fromDate"] > date('Y-m-d')) {
                $this->errors[] = "From date should not be greater than Today Date";
            }
            if ($input["toDate"] > date('Y-m-d')) {
                $this->errors[] = "ToDate should not be greater than Today Date";
            }
        }
        return $this;
    }

    /**
     * Check if fromtime is less than totime required.
     * @param string $fromtime Format.
     * @param string $totime Format.
     * @return object Self.
     */
    public function compareTime($fromtime,$totime)
    {
        if ($fromtime && $totime) {
            $input["fromtime"] = convertDateTimeFormat($fromtime, "h:i A", "H:i:s");
            $input["totime"] = convertDateTimeFormat($totime, "h:i A", "H:i:s");
            if($input["fromtime"] > $input["totime"]){
               $this->errors[] = "From time should not be greater than To time";
            }
            return $this;
        }
    }

    /**
     * Check request fields not blank.
     * @param object $payload Format.
     * @return object Self.
     */
    public function checkRequest($payload){
        if (empty($payload)) {
            $this->errors[] = "Invalid request.";
        }
        return $this;
    }

}
