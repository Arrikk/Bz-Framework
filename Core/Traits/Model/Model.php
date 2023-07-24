<?php

namespace Core\Traits\Model;

use Core\Helpers\Paginate;
use Core\Http\Res;
use Core\Traits\Filter;
use Google\Service\AIPlatformNotebooks\Status;

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
    protected static $pagination = null;


    public function __construct($table = null)
    {
        self::$isTable = $table;
    }

    public static function use(string $tableName)
    {
        self::$isTable = $tableName;
        return new static();
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
    public static function find(array $array = [], string $query = '*', bool $exec = true)
    {
        $total = 0;
        if (self::$pagination) :
            $array['$.order'] = self::$pagination->order;
            $array['$.limit'] = self::$pagination->limit;
            $total = self::select('count(*) as totalItems', self::table());
            foreach ($array as $key => $value) {
                $key = str_replace('$.', '', $key);
                $key = explode('.', $key);
                $keyPref = $key;
                $key = $key[0];
                if ($key == 'where' || $key == 'and' || $key == 'or' || $key == 'in') :
                    if (isset($keyPref[1])) {
                        if ($value)
                            $total->{$keyPref[0]}("$keyPref[1] = '$value'");
                    } else {
                        if ($value) $total->{$key}($value);
                    }
                else :
                endif;
                continue;
                echo $key;
            };
            $total = $total->obj()->exec();
        endif;

        $prep = static::select($query, self::table());
        if (!empty($array)) {
            $prep = static::runMultiple($array, $prep);
        }
        if ($exec) :
            $prep = $prep->exec();
            if(self::$pagination) return (object)[
                'items' => $prep,
                'page' => self::$pagination->page,
                'limit' => self::$pagination->pageLimit,
                'offset' => self::$pagination->offset,
                'totalItems' => (int) $total->totalItems
            ];
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
    public static function findOne(array $array, $type = '*', bool $exec = true)
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
    public static function findById(int $id, string $field = 'id', string $extract = '*', bool $exec = true)
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
    public static function findByEmail(string $email, $extract = '*', bool $exec = true)
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
    public static function findAndUpdate(array $array, array $update, bool $exec = true)
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
    public static function findAndDelete(array $array, bool $exec = true)
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
    public static function inset($value,  $field)
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
    public static function dump(array $array, string $table = '', bool $exec = true)
    {
        if ($table == '') $table = static::table();
        $save = static::create($table, $array);
        if ($exec) :
            $save = $save->lid()->exec();
            return static::findById($save);
        endif;
        return $save->get();
    }

     /**
     * Create many rows
     * @param array $array fields array = [['email' => EMAIL, 'name' => NAME], ['name' => ']] etc
     * @param string $table.. dump to another table insead of set table
     * @return array return boolean, string or an object depending 
     * on your second args and last
     */
    public static function dumpMany(array $array, string $table = '', bool $exec = true)
    {
        $self = new static;
        $self->set('createMany', true);

        if ($table == '') $table = static::table();
        $save = $self->create($table, $array);
        if ($exec) :
            $save = $save->exec();
            return self::whereIn('id', $save);
        endif;
        return $save->get();
    }

       /**
     * Update many rows
     * @param array $array fields array = [['email' => EMAIL, 'name' => NAME], ['name' => ']] etc
     * @param string $table.. dump to another table insead of set table
     * @return array return boolean, string or an object depending 
     * on your second args and last
     */
    public static function updateMany(array $array, $conditions = [], string $table = '',  bool $exec = true)
    {
        $self = new static;
        $self->set('createMany', true);

        if ($table == '') $table = static::table();
        $save = $self->update($table, $array);

        if (is_array($conditions) && count($conditions) > 0)
            foreach ($conditions as $condition => $value) {
                $save->{$condition}($value);
            }
        if ($exec) :
            $save = $save->exec();
            return self::whereIn('id', $save);
        endif;
        return $save->get();
    }

    public static function whereIn(string $column, array $value) {
        $data = (string) implode(',' , $value);
        return self::find([ '$.where' => self::in($column, $data)]);
    }


    public static function col($col)
    {
        self::$col = $col;
        return new static;
    }

    public function modify($update){
        return self::findAndUpdate(['id' => $this->id], $update);
    }

    /**
     * Get paginated format of a data
     * @param int $page, the current page to start from
     * @param int $limit, the maximum number of data to be returned
     * @param string $orderCol, Column to set order
     * @param string $order, the order of line to be returned (ASC, DESC)
     * @param int $offset, the offset of page to start
     */
    public static function paginate(int $page = 1, int $limit = LIMIT, string $orderCol = 'id', string $order = DESC, int $offset = 0)
    {
        self::$pagination = Paginate::page([
            'page' => $page,
            'limit' => $limit,
            'orderCol' => $orderCol,
            'order' => $order,
            'offset' => $offset
        ]);

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

    public static function in(string $column,  $value)
    {
        if (!$value || $value === null || $value == null) return;
        return "$column IN ($value)";
    }
    protected static function notIn(string $in, $qry)
    {
        if (!$qry || $qry === null || $qry == null) return;
        return "$in NOT IN ($qry)";
    }

    public static function like($col, $val)
    {
        return "$col LIKE '%$val%'";
    }

    public static function decoded()
    {
        return self::get();
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
