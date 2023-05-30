<?php

namespace Core\Interfaces;

use Core\Pipes\PipeValidations;

interface PipeValidationInterface
{
    public function isint(string $message = null): PipeValidations;
    public function isnumeric(string $message = null): PipeValidations;
    public function isfloat(string $message = null): PipeValidations;
    public function isstring(int $message = null): PipeValidations;
    public function max(int $max = 10, string $message = null): PipeValidations;
    public function min(int $min = 4, string $message = null): PipeValidations;
    public function gte(int $num, string $message = null): PipeValidations;
    public function lte(int $num, string $message = null): PipeValidations;
    public function isemail(string $message = null): PipeValidations;
    public function is_strong_password(string $message = null): PipeValidations;
    public function isurl(string $message = null): PipeValidations;
    public function isequal($comparison, string $message = null): PipeValidations;
    public function isenum(): PipeValidations;
    public function tolower(): PipeValidations;
    public function toupper(): PipeValidations;
    public function toint(): PipeValidations;
    public function tofloat(): PipeValidations;
    public function tostring(): PipeValidations;
    public function tocapitalized(): PipeValidations;
    public function tocamel(): PipeValidations;
    public function tostudly(): PipeValidations;
    public function match($regex): PipeValidations;
    public function replace($regex, $value = ''): PipeValidations;
    public function default($default): PipeValidations;
    public function contains($value): PipeValidations;
    public function includes($value): PipeValidations;
    public function has($value): PipeValidations;
}
