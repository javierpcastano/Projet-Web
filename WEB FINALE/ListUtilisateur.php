<?php
session_start();
header('Content-Type: application/json');


$jsonString = file_get_contents('users.json');
$data = json_decode($jsonString, true);

$filtered_users = array_filter($data, function($user) {


    foreach ($user['role'] as $r) {
        if (stripos($r, 'DemandeChef') !== false || stripos($r, 'DemandeTraducteur') !== false) {
            return true;
        }

}});

echo json_encode(["users" => array_values($filtered_users)]);

?>
