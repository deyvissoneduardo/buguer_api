<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CPFValidator implements Rule
{
    public function passes($attribute, $value)
    {
        return $this->validateCPF($value);
    }

    public function message()
    {
        return 'CPF is not valid';
    }

    private function validateCPF($value)
    {
        $value = preg_replace('/[^0-9]/', '', $value);

        if (strlen($value) != 11) {
            return false;
        }
        $digits = str_split($value);
        $sum = 0;
        $cpfLength = 10;

        for ($i = 0; $i < 9; $i++) {
            $sum += $digits[$i] * $cpfLength;
            $cpfLength--;
        }

        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($digits[9] != $digit1) {
            return false;
        }

        $sum = 0;
        $cpfLength = 11;

        for ($i = 0; $i < 10; $i++) {
            $sum += $digits[$i] * $cpfLength;
            $cpfLength--;
        }

        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($digits[10] != $digit2) {
            return false;
        }

        return true;
    }
}

?>
