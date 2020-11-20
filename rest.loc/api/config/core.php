<?php

// включаем показ ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// УРЛ домашней страницы
$home_url="http://rest.loc/api/";

// страница указана в параметре URL, по дефолту - одна
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// ставим число записей на страницу
$records_per_page = 5;

// расчет по пределу записей
$from_record_num = ($records_per_page * $page) - $records_per_page;
?>
