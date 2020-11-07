<?php
    $token = filter_input(INPUT_POST, "token");
    if ($token === "") {
        $file = filter_input(INPUT_POST, "file");
        $result = unlink($file);
        echo json_encode(["result" => $result]);
    } else {
        echo json_encode(["result" => false]);
    }
