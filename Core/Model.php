<?php

namespace Core;

use PDO;
use App\Config;
use PDOException;

/**
 * *************** Base Model*******************
 * ==============================================
 * ============== Code Hart (Bruiz) =============
 * ==============================================
 */

 abstract class Model
 {
     protected $i = 0;
     protected $or;
     protected $and;
     protected $where;
     protected $limit;
     protected $query;
     protected $fields;
     protected $class;
     protected $obj;
     protected $assoc;
     protected $both;
     protected $last;
     protected static $concat = 'concat';
     protected static $replace = 'replace';
     protected static $math = 'math';


    public function __call($method, $args)
    {
       return $this->call($method, $args);
    }

    public static function __callStatic($method, $args)
    {
        // call_user_func_array([new static(), '_'.$method], $args);
        return (new static())->call($method, $args);
    }

    private function call($method, $args){
        $method = '_'.$method;
        if(method_exists($this, $method)){
            return $this->{$method}(...$args);
        }
        echo "Method ".$method." does not exist";
    }

   /**
    * Get all Post as an associative array
    * @return object
    */ 
    protected static function _db()
    {
        static $db = null;

        // if($db === null){
          try{

            $db = new PDO('mysql:host=' .Config::DB_HOST. ';dbname=' .Config::DB_NAME. ';charset=utf8', Config::DB_USER, Config::DB_PASSWORD);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $db;

          }
          catch(PDOException $e){
              echo "Connection Attempt Failed ". $e->getMessage();
          }
        // }
    }

    /**
     * Create/insert 
     * 
     */
    private function _create(string $table, array $data = [])
    {
        
        $fields = implode(',', array_keys($data));
        $values = ':'.implode(', :', array_keys($data));

        $query = "INSERT INTO `{$table}` ({$fields}) VALUES ({$values})";

        $this->query = $query;
        $this->fields = $data;
        return $this;
    }

    /**
     * Select from 
     */
    private function _select(string $query, string $table = '', array $fields = [])
    {
        $column = '';
        foreach($fields as $key => $col){
            $column = "`{$key}` = :{$key}";
        }

        $query = "SELECT $query";
        if($table !== ''){
            $query .= " FROM `$table`";
        }
        if(!empty($column)){
            $query .= " WHERE $column";
        }
        $this->query = $query;
        $this->fields = $fields;
        $this->select = true;
        return $this;
    }

    private function _update(string $table, array $fields = [], $opt = false){
        $column = '';
        $i = 1;
        foreach ($fields as $key => $value) {
            if($opt){
                if( is_string($opt) && $opt == self::$concat):
                    $column .= "`{$key}` = " . $this->concat($key, ":{$key}");
                    elseif( is_array($opt) && $opt['name'] == self::$math):
                        $column .= "`{$key}` = ". $this->mathWith($key, $opt['operator'], ":{$key}");
                    elseif( is_array($opt) && $opt['name'] == self::$replace):
                        $column .= "`{$key}` = ". $this->replace($key, $opt['from'], ":{$key}");
                endif;
            }else{
                $column .= "`{$key}` = :{$key}";
            }

            if($i < count($fields)){
                $column .= ', ';
            }
            $i++;
        }

        $this->query = "UPDATE `{$table}` SET $column";
        $this->fields = $fields;
        return $this;
    }

    private function _trash(string $table, array $fields = [])
    {
        $column = '';
        foreach($fields as $key => $col){
            $column = "`{$key}` = :{$key}";
        }

        $query = "DELETE FROM `$table`";
        if(!empty($column)){
            $query .= " WHERE $column";
        }
        $this->query = $query;
        $this->fields = $fields;
        return $this;
    }

    protected function _get(){
        return [
            'query' => $this->query,
            'field' => $this->fields
        ];
    }

    private function _from(string $from){
        $this->query .= " FROM $from";
        return $this;
    }

    private function _clause($column, $type, $value, $operator){
        $val = str_replace('.', '', $column);
        if(array_key_exists($val, $this->fields)){
            $val = $val.$this->i;
            $this->i++;
        }
        $stmt = $column;
        if($value):
            $stmt = "`{$column}` $operator :{$val}";
            $fields = [$val => $value ];
            $this->fields = array_merge($fields, $this->fields);
        endif;

        $this->query .= " $type {$stmt}";
        return true;
    }

    private function _where(string $fields, $value = null, $operator = '=')
    {
        $this->clause($fields, 'WHERE', $value, $operator);
        return $this;
    }
    private function _and(string $fields, $value = null, $operator = '=')
    {
        $this->clause($fields, 'AND', $value, $operator);
        return $this;
    }
    private function _or(string $fields, $value = null, $operator = '=')
    {
        $this->clause($fields, 'OR', $value, $operator);
        return $this;
    }
    private function _btw(string $fields, $opt)
    {
        $this->query .= " BETWEEN($fields) AND $opt";
        return $this;
    }
    private function _limit(string $limit)
    {
        $this->query .= " LIMIT $limit";
        return $this;
    }
    private function _order(string $order)
    {
        $this->query .= " ORDER BY $order";
        return $this;
    }
    private function _group(string $group)
    {
        $this->query .= " GROUP BY $group";
        return $this;
    }
    private function _left(string $left)
    {
        $this->query .= " LEFT JOIN $left";
        return $this;
    }
    private function _right($right)
    {
        $this->query .= " RIGHT JOIN $right";
        return $this;

    }
    private function _outer($out)
    {
        $this->query .= " OUTER JOIN $out";
        return $this;
    }
    private function _inner($inner)
    {
        $this->query .= " INNER JOIN $inner";
        return $this;
    }
    private function _on($on){
        $this->query .= " ON $on";
        return $this;
    }
    private function _inset($needle, $heystack){
        $this->query .= "FIND_IN_SET('$needle', $heystack)";
        return $this;
    }
    private function _concatWs($column, $string){
        return "CONCAT_WS($column, '$string')";
    }
    private function _concat($column, $string){
        return "CONCAT($column, $string)";
    }
    private function _replace($column, $from, $to){
        return "REPLACE($column, '$from', $to)";
    }
    private function _mathWith($column, $operator, $value)
    {
        return "$column $operator $value";
    }
    private function _obj(){
        $this->obj = true;
        return $this;
    }
    private function _class(){
        $this->class = true;
        return $this;
    }
    private function _both(){
        $this->both = true;
        return $this;
    }
    private function _assoc(){
        $this->assoc = true;
        return $this;
    }
    private function _lid(){
        $this->last = true;
        return $this;
    }

    protected function exec(){

        $db =  $this->db();
        $stmt = $db->prepare($this->query);
        foreach ($this->fields as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        if($this->select ?? false){
            $stmt->execute();
            if($this->class && $this->class !== null){
                $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
                return $stmt->fetch();
            }elseif($this->both && $this->both !== null){
                return $stmt->fetchAll(PDO::FETCH_BOTH);
            }elseif($this->assoc && $this->assoc !== null){
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }elseif($this->obj || $this->obj !== null){
                return $stmt->fetch(PDO::FETCH_OBJ);
            }else{
                return $stmt->fetchAll(PDO::FETCH_OBJ);
            }
        }else{
            if($this->last){
                $stmt->execute();
                return $db->lastInsertId();
            }
            return $stmt->execute();
        }
    }
    /**
     * serialize Input
     * 
     * @return string
     */
    private function _clean($input){
        $input = \htmlspecialchars($input);
        $input = \trim($input);
        $input = \stripslashes($input);
        return $input;
    }
 }