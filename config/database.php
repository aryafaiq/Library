<?php

$DB_CONFIG = [
    "host" => "localhost",
    "username" => "root",
    "password" => "",
    "db_name" => "e_library"
];

$DB_CONN = mysqli_connect(
    $DB_CONFIG["host"], 
    $DB_CONFIG["username"], 
    $DB_CONFIG["password"], 
    $DB_CONFIG["db_name"]
);

if(!$DB_CONN){
    mysqli_connect_error($DB_CONN);
}

