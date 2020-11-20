<?php

// HTTP-заголовки 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// подключение файлов для соединения с базой данных и файл с объектом Category 
include_once '../config/database.php';
include_once '../objects/category.php';

// создание подключения к БД
$database = new Database();
$db = $database->getConnection();

// инициализация объекта категории
$category = new Category($db);

// запросы для категорий 
$stmt = $category->read();
$num = $stmt->rowCount();

// проверка на записи 
if ($num>0) {

    // массив 
    $categories_arr=array();
    $categories_arr["records"]=array();

    // получение содержимого таблицы
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        
        // извлечение строки
        extract($row);

        $category_item=array(
            "id" => $id,
            "name" => $name,
            "description" => html_entity_decode($description)
        );

        array_push($categories_arr["records"], $category_item);
    }

    // выдача 200 OK 
    http_response_code(200);

    // вывод данных категорий через json 
    echo json_encode($categories_arr);
} else {

    // ответ 404
    http_response_code(404);

    // для отсуствия категорий 
    echo json_encode(array("message" => "Категории не найдены."), JSON_UNESCAPED_UNICODE);
}
?>
