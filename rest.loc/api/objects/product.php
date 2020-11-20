<?php
class Product {

    // подключение к БД и таблице продуктов 
    private $conn;
    private $table_name = "products";

    // ставим свойства объекта 
    public $id;
    public $name;
    public $description;
    public $price;
    public $category_id;
    public $category_name;
    public $created;

    // конструкуторе соединения с БД 
    public function __construct($db){
        $this->conn = $db;
    }

   // получаем товары
function read(){

    // выборка всех записей
    $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            ORDER BY
                p.created DESC";

    // подготовка запроса
    $stmt = $this->conn->prepare($query);

    // выполнение запроса 
    $stmt->execute();

    return $stmt;
}
    
    // создаем товары
function create(){

    // запрос к БД для вставки записей 
    $query = "INSERT INTO
                " . $this->table_name . "
            SET
                name=:name, price=:price, description=:description, category_id=:category_id, created=:created";

    // подготовка запроса 
    $stmt = $this->conn->prepare($query);

    // очищаем от инъекций
    $this->name=htmlspecialchars(strip_tags($this->name));
    $this->price=htmlspecialchars(strip_tags($this->price));
    $this->description=htmlspecialchars(strip_tags($this->description));
    $this->category_id=htmlspecialchars(strip_tags($this->category_id));
    $this->created=htmlspecialchars(strip_tags($this->created));

    // привязываем значения 
    $stmt->bindParam(":name", $this->name);
    $stmt->bindParam(":price", $this->price);
    $stmt->bindParam(":description", $this->description);
    $stmt->bindParam(":category_id", $this->category_id);
    $stmt->bindParam(":created", $this->created);

    // выполняем запрос 
    if ($stmt->execute()) {
        return true;
    }

    return false;
}
    
    // заполнение формы обновления
function readOne() {

    // запрос к БД для чтения одной записки
    $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            WHERE
                p.id = ?
            LIMIT
                0,1";

    // подготовка запроса 
    $stmt = $this->conn->prepare( $query );

    // привязываем айдишку обновляемого товара
    $stmt->bindParam(1, $this->id);

    // делаем запрос 
    $stmt->execute();

    // получаем строку 
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // ставим свойста объекта
    $this->name = $row['name'];
    $this->price = $row['price'];
    $this->description = $row['description'];
    $this->category_id = $row['category_id'];
    $this->category_name = $row['category_name'];
}
    
    // обновление товара
function update(){

    // запрос для обновления записи
    $query = "UPDATE
                " . $this->table_name . "
            SET
                name = :name,
                price = :price,
                description = :description,
                category_id = :category_id
            WHERE
                id = :id";

    // подготовливаем запроса 
    $stmt = $this->conn->prepare($query);

    // защита от инъекций
    $this->name=htmlspecialchars(strip_tags($this->name));
    $this->price=htmlspecialchars(strip_tags($this->price));
    $this->description=htmlspecialchars(strip_tags($this->description));
    $this->category_id=htmlspecialchars(strip_tags($this->category_id));
    $this->id=htmlspecialchars(strip_tags($this->id));

    // привязываем значения 
    $stmt->bindParam(':name', $this->name);
    $stmt->bindParam(':price', $this->price);
    $stmt->bindParam(':description', $this->description);
    $stmt->bindParam(':category_id', $this->category_id);
    $stmt->bindParam(':id', $this->id);

    // осуществляем запрос 
    if ($stmt->execute()) {
        return true;
    }

    return false;
}
    
    // удаление товара
function delete(){

    // запрос к БД для удаления 
    $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

    // подготовка
    $stmt = $this->conn->prepare($query);

    // от инъекций
    $this->id=htmlspecialchars(strip_tags($this->id));

    // привязываем айдишку для удаления
    $stmt->bindParam(1, $this->id);

    // делаем запрос 
    if ($stmt->execute()) {
        return true;
    }

    return false;
}
    
    // поиск товаров
function search($keywords){

    // запрос по выборке для БД
    $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            WHERE
                p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?
            ORDER BY
                p.created DESC";

    // готовим запрос 
    $stmt = $this->conn->prepare($query);

    // чистим
    $keywords=htmlspecialchars(strip_tags($keywords));
    $keywords = "%{$keywords}%";

    // привязываем
    $stmt->bindParam(1, $keywords);
    $stmt->bindParam(2, $keywords);
    $stmt->bindParam(3, $keywords);

    // делаем запрос 
    $stmt->execute();

    return $stmt;
}
    
    // чтение с пагинацией 
public function readPaging($from_record_num, $records_per_page){

    // запрос с БД 
    $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            ORDER BY p.created DESC
            LIMIT ?, ?";

    // готовим 
    $stmt = $this->conn->prepare( $query );

    // связываем переменнные
    $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

    // запрашиваем 
    $stmt->execute();

    // возвращаем инфу с БД
    return $stmt;
}
    
    // функция пагинации товаров
public function count(){
    $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";

    $stmt = $this->conn->prepare( $query );
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['total_rows'];
}
    
    
}
?>
