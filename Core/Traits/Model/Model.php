<?php

namespace Core\Traits\Model;

use Core\Http\Res;
use Core\Traits\Filter;

/**
 * *************** Trait Class*******************
 * ==============================================
 * ============== Code Hart (Bruiz) =============
 * ==============================================
 */

trait Model
{

    use Filter;
    /**
     * ============ Usage and parameters =============
     * 
     * *********** $ sign usage *****************
     * $ = WHERE {query},
     * $.and = AND {query} e.g (AND date BETWEEN(NOW())) etc...
     * $.or = OR {query}
     * $.group = GROUP BY
     * $.limit = LIMIT()
     * $.order = ORDER BY
     * 
     * *************** field usage ***************
     * field = WHERE {field = :field => value}
     * and.field AND {field = :field => value}
     * or.field OR {field = :field => value}
     */

    private static $col = 'id';
    protected static $isTable = null;


    public function __construct($table = null)
    {
        self::$isTable = $table;
    }

    public static function use(string $tableName)
    {
        self::$isTable = $tableName;
        return new static($tableName);
    }

    private static function table()
    {
        $class = explode('\\', get_called_class());
        $class = strtolower(end($class)) . 's';

        $table = self::$isTable ? self::$isTable : ($class ?? static::$table);
        return $table;
    }

    /**
     * Find all data from a table
     * @param array $array (optional) Where to find from
     * set $self to false to return bool true or false instead
     * @return mixed integer if $self is true and bool otherwise
     */
    protected static function find(array $array = [], string $query = '*', bool $exec = true)
    {
        $prep = static::select($query, self::table());
        if (!empty($array)) {
            $prep = static::runMultiple($array, $prep);
        }
        if ($exec) :
            $prep = $prep->exec();
            return $prep;
        endif;
        return $prep->get();
    }

    /**
     * Find a data from a table
     * @param array $array (optional) Where to find.
     * e.g find from users where email = :email (['email' => EMAIL]) etc
     * @param string $query (query type) e.g *, count, sum, column etc
     * default select (*) all
     * @param bool $exec set to defaultly to true. set to false only to see your query
     * @return object
     */
    public function _findOne(array $array, $type = '*', bool $exec = true)
    {
        if (in_array('$.from', array_keys($array))) {
            $prep = static::select($type, '');
        } else {
            $prep = static::select($type, self::table());
        }

        if (!empty($array)) {
            $prep = static::runMultiple($array, $prep);
        }
        if ($exec) :
            $prep = $prep->class()->exec();
            if ($prep) $prep->object($prep);
            return $prep;
        endif;
        return $prep->get();
    }

    /**
     * Find By an id 
     * @param int $id id to find
     * @param string $field .. what is you id table name .. (id)default
     * @param string $extract datas to extract e.g *, email, count(id) etc
     * @param bool $exec set to defaultly to true. set to false only to see your query
     * @return object
     */
    protected static function findById(int $id, string $field = 'id', string $extract = '*', bool $exec = true)
    {
        if (isset(self::$col) && self::$col) $field = self::$col;
        return static::findOne([$field => $id], $extract, $exec);
    }

    /**
     * Find By an email
     * @param string $email id to find
     * @param string $extract datas to extract e.g *, email, count(id) etc
     * @param bool $exec set to defaultly to true. set to false only to see your query
     * @return object
     */
    protected static function findByEmail(string $email, $extract = '*', bool $exec = true)
    {
        return static::findOne(['email' => $email], $extract, $exec);
    }

    /**
     * Find and Update
     * @param array $array option to find
     * @param array $update datas to update e.g array('user_name' => 'USERNAME', 'id' => 2) etc
     * @param bool $exec set to defaultly to true. set to false only to see your query
     * @return mixed
     */
    protected static function findAndUpdate(array $array, array $update, bool $exec = true)
    {
        $prep = static::update(self::table(), $update);
        $prep = static::runMultiple($array, $prep);
        if ($exec) :
            $prep = $prep->exec();
            return static::findOne($array);
        endif;
        return $prep->get();
    }

    /**
     * Find and Delete
     * @param array $array option to find
     * @param bool $exec set to defaultly to true. set to false only to see your query
     * @return mixed
     */
    protected static function findAndDelete(array $array, bool $exec = true)
    {
        $prep = static::trash(self::table());
        $prep = static::runMultiple($array, $prep);
        if ($exec) :
            $prep = $prep->exec();
            return $prep;
        endif;
        return $prep->get();
    }

    /**
     * More Query Method FIND_IN_SET
     * @return string
     */
    protected static function inset($value,  $field)
    {
        return "FIND_IN_SET('$value', $field)";
    }

    /**
     * More Query Method , Search Between
     * @param string $field, Where to search
     * @param string $range, Range of search
     * @return string
     */
    protected static function between($field, $range)
    {
        if (!$field || $field === null || $field == null) return;
        if (!$range || $range === null || $range == null) return;
        return "`$field` BETWEEN $range";
    }

    protected static function range($from, $to)
    {
        if (!$from || $from === null || $from == null) return;
        if (!$to || $to === null || $to == null) return;
        return "$from AND $to";
    }

    /**
     * Order randomly
     * @return string
     */
    protected static function rand()
    {
        return "RAND()";
    }

    /**
     * Create a new row
     * @param array $array fields array = ['email' => EMAIL, 'name' => NAME] etc
     * @param string $table.. dump to another table insead of set table
     * @return mixed return boolean, string or an object depending 
     * on your second args and last
     */
    protected static function dump(array $array, string $table = '', bool $exec = true)
    {
        if ($table == '') $table = static::table();
        $save = static::create($table, $array);
        if ($exec) :
            $save = $save->lid()->exec();
            return static::findById($save);
        endif;
        return $save->get();
    }

    public static function col($col)
    {
        self::$col = $col;
        return new static;
    }

    // /**
    //  * Create a new row
    //  * @param array $array fields array
    //  * @param string $table ... table to save
    //  * @return mixed
    //  */
    // protected static function save(array $array, string $table = '')
    // {
    //     if($table == '') $table = static::$table;
    //     $save = static::create($table, $array)
    //     ->lid()->exec();
    //     return static::findById($save);
    // }

    protected static function in(string $in,  $qry)
    {
        if (!$qry || $qry === null || $qry == null) return;
        return "$in IN ($qry)";
    }
    protected static function notIn(string $in, $qry)
    {
        if (!$qry || $qry === null || $qry == null) return;
        return "$in NOT IN ($qry)";
    }


    protected static function runMultiple($toRun, $prep)
    {
        foreach ($toRun as $key => $value) :
            if (empty($value) || $value == null) continue;

            $getQuery = explode('.', $key);
            if (count($getQuery) > 1) :
                $query = preg_replace('/[0-9]/', '', $getQuery[0]);
                $field = preg_replace('/[0-9]/', '', $getQuery[1]);
                if ($query == '$') :
                    $prep->$field($value);
                else :
                    $prep->$query($field, $value);
                endif;
            else :
                if ($key == '$') :
                    $prep->where($value);
                else :
                    $prep->where($key, $value);
                endif;
            endif;
        endforeach;

        return $prep;
    }
}
