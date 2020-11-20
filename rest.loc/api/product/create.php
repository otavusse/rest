<?php
// HTTP-заголовки 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// БД
include_once '../config/database.php';

// создаём объект товара 
include_once '../objects/product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
 
// получаем данные
$data = json_decode(file_get_contents("php://input"));
 
// проверяем на пустоту
if (
    !empty($data->name) &&
    !empty($data->price) &&
    !empty($data->description) &&
    !empty($data->category_id)
) {

    // ставим значения по свойствам
    $product->name = $data->name;
    $product->price = $data->price;
    $product->description = $data->description;
    $product->category_id = $data->category_id;
    $product->created = date('Y-m-d H:i:s');

    // создаём товар
    if($product->create()){

        // код 201
        http_response_code(201);

        // месседж юзеру
        echo json_encode(array("message" => "Товар был создан."), JSON_UNESCAPED_UNICODE);
    }

    // ответ пользователю при ошибке
    else {

        http_response_code(503);

        echo json_encode(array("message" => "Невозможно создать товар."), JSON_UNESCAPED_UNICODE);
    }
}

// неполные данные со стороны пользователя
else {

    // установим код ответа - 400 неверный запрос 
    http_response_code(400);

    // сообщим пользователю 
    echo json_encode(array("message" => "Невозможно создать товар. Данные неполные."), JSON_UNESCAPED_UNICODE);
}
?>
