<?php
    $token = filter_input(INPUT_POST, "token");
    $server_path = "/home/servermonster.online/public_html";
    $server_link = "https://servermonster.online/assistironline/";
    if ($token === "") {
        $link_download = filter_input(INPUT_POST, "link");
        $file = filter_input(INPUT_POST, "file");
        $arquivo_pasta = $server_path . "/" . $file;
        $comando = "axel '$link_download' --output= $arquivo_pasta, -o $arquivo_pasta";
        shell_exec($comando);
        $link_final = $server_link . $file;
        echo json_encode(["link" => $link_final]);
    } else {
        echo json_encode(["link" => ""]);
    }
