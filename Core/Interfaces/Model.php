<?php
namespace Interfaces;

interface Model {
    public function db($var);
    public function or(string $var);
    public function and(string $var);
    public function replace(string $var);
    public function where(string $var);
    public function between(string $var);
    public function limit(string $var);
    public function group(string $var);
    public function order(string $var);
    public function runMultiple(string $var);
    public function inset(string $var);
    public function like(string $var);
    public function math(string $var);
    public function exec(string $var);
    public function select(string $var);
    public function update(string $var);
    public function query(string $var);
    public function fields(string $var);
    public function class();
    public function object();
    public function assoc();
    public function both();
    public function last();
    public function concat();
    public function concatWs();
    public function trash();
    public function lid();
    public function inner();
    public function outer();
    public function left();
    public function right();
    public function full();
}