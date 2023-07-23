<?php

namespace app\supports;

use app\traits\Validations;

class Validate
{

    use Validations;
    private array $dataValidations = [];

    public function validations(array $validations)
    {
        foreach ($validations as $field => $validation) {
            $param = '';
            if (!str_contains($validation, "|")) {
                if (str_contains($validation, ":")) {
                    list($validation, $param) = explode(":", $validation);
                    $param = str_contains($param, ",") ? explode(",", trim($param)) : $param;
                }
                $this->dataValidations[$field] = $this->$validation($field, $param);
            } else {
                $othersValidations = explode("|", $validation);
                foreach ($othersValidations as $validation) {
                    if (str_contains($validation, ":")) {
                        list($validation, $param) = explode(":", $validation);
                        $param = str_contains($param, ",") ? explode(",", trim($param)) : $param;
                    }
                    $this->dataValidations[$field] = $this->$validation($field, $param);
                    if ($this->dataValidations[$field] === false)
                        break;
                }
            }
        }
        if (in_array(false, $this->dataValidations, true)) {
            return ['validations' => false, 'msgError' => $this->msgValidations];
        }
        return  ['validations' => true, 'data' => $this->dataValidations];
    }
}
