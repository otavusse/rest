<?php
class Category{

    // соединение с базой данных и таблицей категорий
    private $conn;
    private $table_name = "categories";

    // ставим свойства объекта
    public $id;
    public $name;
    public $description;
    public $created;

    public function __construct($db){
        $this->conn = $db;
    }

    // список выбора 
    public function readAll(){
        
        // выборка по данным 
        $query = "SELECT
                    id, name, description
                FROM
                    " . $this->table_name . "
                ORDER BY
                    name";
 
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
 
        return $stmt;
    }
    
    // применяем список выбора 
public function read(){

    // выборка по данным
    $query = "SELECT
                id, name, description
            FROM
                " . $this->table_name . "
            ORDER BY
                name";

    $stmt = $this->conn->prepare( $query );
    $stmt->execute();

    return $stmt;
}
}
?>
