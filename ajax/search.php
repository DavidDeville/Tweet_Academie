<?php

require_once '../controller/FormSearchController.class.php';

$search = new FormSearchController();

$search->is_valid();

echo json_encode(
    $search->status()
);

?>