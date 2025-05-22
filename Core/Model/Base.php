<?php

namespace Core\Model;

use AllowDynamicProperties;
use PDO;
use App\Config;
use Core\Env;
use Core\Http\Res;
use PDOException;

/**
 * *************** Base Model*******************
 * ==============================================
 * ============== Code Hart (Bruiz) =============
 * ==============================================
 */

#[AllowDynamicProperties]
abstract class Base
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
    protected $in;
    protected $createMany = false;
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

    private function call($method, $args)
    {
        $method = '_' . $method;
        if (method_exists($this, $method)) {
            return $this->{$method}(...$args);
        }
        echo "Method " . $method . " does not exist";
    }

    /**
     * Get all Post as an associative array
     * @return object
     */
    protected static function _db()
    {
        static $db = null;

        // if($db === null){
        try {
            // Config::clearDB();
            $db = new PDO('mysql:host=' . Env::DB_HOST() . ';dbname=' . Env::DB_NAME() . ';charset=utf8mb4;port=' . Env::DB_PORT(), Env::DB_USER(), ENV::DB_PASSWORD());
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->exec("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
            return $db;
        } catch (PDOException $e) {
            Res::send("Connection Attempt Failed " . $e->getMessage());
        }
        // }
    }

    /**
     * Create/insert 
     * 
     */
    private function _create(string $table, array $data = [])
    {

        $set = $data;
        if ($this->createMany) :
            $newData = [];
            foreach ($data as $child) :
                $newData[] = (array) $child;
            endforeach;
            $data = $newData;
            $set = $data[0];
            unset($newData);

        endif;

        $fields = '`' . implode('`, `', array_keys($set)) . '`';
        $values = ':' . implode(', :', array_keys($set));

        $query = "INSERT INTO `{$table}` ({$fields}) VALUES ({$values})";

        $this->query = $query;
        $this->fields = $data;
        return $this;
    }

    public function set($name, $value)
    {
        $this->{$name} = $value;
    }


    /**
     * Select from 
     */
    private function _select(string $query, string $table = '', array $fields = [])
    {
        $column = '';
        foreach ($fields as $key => $col) {
            $column = "`{$key}` = :{$key}";
        }

        $query = "SELECT $query";
        if ($table !== '') {
            $query .= " FROM $table";
        }
        if (!empty($column)) {
            $query .= " WHERE $column";
        }
        $this->query = $query;
        $this->fields = $fields;
        $this->select = true;
        return $this;
    }

    private function _update(string $table, array $fields = [], $opt = false)
    {
        $column = '';
        $i = 1;


        $set = $fields;
        if ($this->createMany) :
            $newfields = [];
            foreach ($fields as $child) :
                $newfields[] = (array) $child;
            endforeach;
            $data = $newfields;
            $set = $data[0];
            unset($newData);

        endif;

        foreach ($set as $key => $value) {
            if ($opt) {
                if (is_string($opt) && $opt == self::$concat) :
                    $column .= "`{$key}` = " . $this->concat($key, ":{$key}");
                elseif (is_array($opt) && $opt['name'] == self::$math) :
                    $column .= "`{$key}` = " . $this->mathWith($key, $opt['operator'], ":{$key}");
                elseif (is_array($opt) && $opt['name'] == self::$replace) :
                    $column .= "`{$key}` = " . $this->replace($key, $opt['from'], ":{$key}");
                endif;
            } else {
                $column .= "`{$key}` = :{$key}";
            }

            if ($i < count($set)) {
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
        foreach ($fields as $key => $col) {
            $column = "`{$key}` = :{$key}";
        }

        $query = "DELETE FROM `$table`";
        if (!empty($column)) {
            $query .= " WHERE $column";
        }
        $this->query = $query;
        $this->fields = $fields;
        return $this;
    }

    protected function _get()
    {
        return (object) [
            'query' => $this->query,
            'field' => $this->fields
        ];
    }

    private function _from(string $from)
    {
        $this->query .= " FROM $from";
        return $this;
    }

    private function _clause($column, $type, $value, $operator, $useRBC = true)
    {
        $val = str_replace('.', '', $column);
        if (array_key_exists($val, $this->fields)) {
            $val = $val . $this->i;
            $this->i++;
        }
        $stmt = $column;
        if ($value) :
            $stmt = "{$column} $operator :{$val}";
            if ($useRBC) $stmt = "`{$column}` $operator :{$val}";
            $fields = [$val => $value];
            $this->fields = array_merge($fields, $this->fields);
        endif;

        $this->query .= " $type {$stmt}";
        return true;
    }

    private function _where(string $fields, $value = null, $operator = '=', $useRBC = true)
    {
        $this->clause($fields, 'WHERE', $value, $operator, $useRBC);
        return $this;
    }
    private function _and(string $fields, $value = null, $operator = '=', $useRBC = true)
    {
        $this->clause($fields, 'AND', $value, $operator, $useRBC);
        return $this;
    }
    private function _or(string $fields, $value = null, $operator = '=', $useRBC = true)
    {
        $this->clause($fields, 'OR', $value, $operator, $useRBC);
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
    private function _join(string $join)
    {
        $this->query .= " JOIN $join";
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
    private function _on($on)
    {
        $this->query .= " ON $on";
        return $this;
    }
    private function _in($in)
    {
        $this->query .= " IN $in";
        return $this;
    }
    private function _offset($offset)
    {
        $this->query .= " OFFSET $offset";
        return $this;
    }
    private function _inset($needle, $heystack)
    {
        $this->query .= "FIND_IN_SET('$needle', $heystack)";
        return $this;
    }
    private function _concatWs($column, $string)
    {
        return "CONCAT_WS($column, '$string')";
    }
    private function _concat($column, $string)
    {
        return "CONCAT($column, $string)";
    }
    private function _replace($column, $from, $to)
    {
        return "REPLACE($column, '$from', $to)";
    }
    private function _mathWith($column, $operator, $value)
    {
        return "$column $operator $value";
    }    
    private function _beginTransaction() {
        return $this->getDB()->beginTransaction();
    }
    private function _commitTransaction() {
        return $this->getDB()->commit();
    }
    private function _rollBackTransaction() {
        return $this->getDB()->rollBack();
    }
    private function _obj()
    {
        $this->obj = true;
        return $this;
    }
    private function _class()
    {
        $this->class = true;
        return $this;
    }
    private function _both()
    {
        $this->both = true;
        return $this;
    }
    private function _assoc()
    {
        $this->assoc = true;
        return $this;
    }
    private function _lid()
    {
        $this->last = true;
        return $this;
    }

    public function exec()
    {

        try {
            //code...
            $db =  $this->db();
            $stmt = $db->prepare($this->query);

            if ($this->createMany) :
                $last = [];
                foreach ($this->fields as $data) {
                    foreach ($data as $key => $value) {
                        $stmt->bindValue(":$key", $value);
                    }
                    $stmt->execute();
                    $last[] = $db->lastInsertId();
                }
                return $last;
            endif;

            foreach ($this->fields as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            if ($this->select ?? false) {
                $stmt->execute();
                if ($this->class) {
                    $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
                    return $stmt->fetch();
                } elseif ($this->both) {
                    return $stmt->fetchAll(PDO::FETCH_BOTH);
                } elseif ($this->assoc) {
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } elseif ($this->obj) {
                    return $stmt->fetch(PDO::FETCH_OBJ);
                } else {
                    // $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
                    return $stmt->fetchAll(PDO::FETCH_OBJ);
                }
            } else {
                if ($this->last) {
                    $stmt->execute();
                    return $db->lastInsertId();
                }
                return $stmt->execute();
            }
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->throwable($th);
        }
    }
    private function getDB(){
        return $this->db();
    }
    /**
     * serialize Input
     * 
     * @return string
     */
    private function _clean($input)
    {
        $input = \htmlspecialchars($input);
        $input = \trim($input);
        $input = \stripslashes($input);
        return $input;
    }
}
