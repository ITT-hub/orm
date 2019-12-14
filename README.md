# Модель для работы с базой данных
Создать свою модель

```php
<?php
use ITTech\ORM\Model;

class User extends Model
{
    /*
     * Таблица модели
     */
    protected $table = "users";
}
```

Инициировать подключение к базе данных

```php
<?php
use ITTech\ORM\Connect;

$data = [
    "host"     => "localhost",
    "port"     => 3306,
    "database" => "test",
    "user"     => "root",
    "password" => "1234",
    "charset"  => "utf8"
];

Connect::create($data);
```
После чего можно обращаться к таблице

```php
/*
 * Выбор из таблицы с условием
 */
$result = User::where("enable", 1)->get();
```

> Допускается несколько условий для выбора

```php
/*
 * Выбор из таблицы с условием
 */
$result = User::where("enable", 1)
            ->where("id", ">", 9)->get();
            
/*
 * Выбор из таблицы с условием
 * Сортировка выбора
 */
$result = User::where("enable", 1)
            ->orderby("name", "DESC")->get();

/*
 * Выбор по идентификатору
 */
$result = User::find(1);
```

Для вставки записи необходимо создать модель, и передать в его свойства (соответствующие полям) значения

```php
$model           = new User();
$model->name     = "Вася";
$model->password = 1234;

$model->save();
```

Для обновления модели просто измените ее свойства и вызовите метод save();

```php
$model       = User::find(1);
$model->name = "Петя";

$model->save();
```

Для удаления модели используйте метод drop()

```php
User::find(1)->drop();
```