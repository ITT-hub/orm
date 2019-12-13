<?php
/*
 * Created 11.12.2019 14:34
 */

namespace IT\Technology\ORM;

/**
 * Class Model
 * @author Alexandr Pokatskiy
 * @copyright ITTechnology
 */
class Model
{
    /**
     * Таблица в базе данных
     * @var null
     */
    protected $table = null;

    /**
     * Массив полей таблицы
     * @var array
     */
    protected $fields = [];

    /**
     * Подключение к базе
     * @var null
     */
    private $connect = null;

    /**
     * Массив запросов с условием
     * @var array
     */
    private static $whereData = [];

    /**
     * Массив моделей
     * @var array
     */
    private $modelOBJ = [];

    /**
     * Порядок сортировки
     * @var string
     */
    private $sort = " ORDER BY id ASC";

    /**
     * Model constructor.
     * @param int|null $id
     */
    public function __construct(int $id = null)
    {
        $this->connect = Connect::getInstance();

        if(!is_null($id))
        {
            $this->getModel($id);
        }
    }

    /**
     * Выбор по идентификатору
     * @param int $id
     * @return $this
     */
    private function getModel(int $id)
    {
        $statement = $this->connect->prepare("SELECT * FROM ".$this->table." WHERE id = :id");
        $statement->execute(["id" => $id]);
        $result = $statement->fetch();
        $keys   = array_keys($result);

        for ($i=0; $i<count($result); $i++)
        {
            $key = $keys[$i];
            $this->$key = $result[$key];
        }

        return $this;
    }

    /**
     * Создать свойства таблицы
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
        array_push($this->fields, $name);
    }

    /**
     * Преобразовать в JSON
     */
    public function toJson()
    {
        $this->modelOBJ = json_encode($this->modelOBJ);
    }

    /**
     * Сохранить модель
     * @return bool|Model
     */
    public function save()
    {
        if(empty($this->id))
        {
            return $this->create();
        } else {
            return $this->update();
        }
    }

    /**
     * Создать новую модель
     * @return bool
     */
    private function create()
    {
        $paramsData = [];
        $data       = [];
        foreach($this->fields as $item)
        {
            $data[$item] = $this->$item;
            array_push($paramsData, ":".$item);
        }
        $param_name_string  = implode(", ", $this->fields);
        $param_value_string = implode(", ", $paramsData);

        $statement = $this->connect->prepare("INSERT INTO ".$this->table." ($param_name_string) VALUES ($param_value_string)");

        return $statement->execute($data);
    }

    /**
     * Обновить текущую модель
     */
    private function update()
    {
        unset($this->fields[0]);

        try {
            foreach($this->fields as $item)
            {
                $statement = $this->connect->prepare("UPDATE ".$this->table." set $item = :$item where id = :id");
                $statement->execute(["id" => $this->id, $item => $this->$item]);
            }
            $this->fields[0] = $this->id;
            ksort($this->fields);
            return $this;
        } catch (\Exception $e)
        {
            echo "Ошибка: ".$e->getMessage();
        }
    }

    /**
     * Выбор записи по идентификатору
     * @param int $id
     * @return Model
     */
    public static function find(int $id)
    {
        return new static($id);
    }

    /**
     * Условия выбора
     * @param $column
     * @param $expression
     * @param null $value
     * @return Model
     */
    public static function where($column, $expression, $value=null)
    {
        if(is_null($value))
        {
            $value      = $expression;
            $expression = "=";
        }

        array_push(self::$whereData, ["column" => $column, "expression" => $expression, "value" => $value]);
        return new static();
    }

    /**
     * Сортировка выдачи
     * @param string $column
     * @param string $type
     * @return $this
     */
    public function orderby(string $column, $type = "ASC")
    {
        $this->sort = " ORDER BY ".$column." ".$type;
        return $this;
    }

    /**
     * Получить результат выбора
     * @return array
     */
    public function get()
    {
        $data      = [];
        $dataValue = [];

        foreach (self::$whereData as $item)
        {
            array_push($data, $item["column"].$item["expression"].":".$item["column"]);
            $dataValue[$item["column"]] = $item["value"];
        }

        $dataSTR   = implode(" AND ", $data);
        $statement = $this->connect->prepare("SELECT * FROM ".$this->table." WHERE ".$dataSTR.$this->sort);

        $statement->execute($dataValue);

        $result    = $statement->fetchAll();

        foreach ($result as $item)
        {
            array_push($this->modelOBJ, new static($item["id"]));
        }

        return $this->modelOBJ;
    }

    /**
     * Удалить модель
     * @return bool
     */
    public function drop(): bool
    {
        $statement = $this->connect->prepare("DELETE FROM ".$this->table." WHERE id = :id");
        return $statement->execute(["id" => $this->id]);
    }
}
