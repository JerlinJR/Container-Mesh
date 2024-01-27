<?php

//TODO:Check for authentication before accessing the file

${basename(__FILE__, '.php')} = function(){
    if($this->get_request_method() == "POST"){
        try {
            $mesh = new MeshCtl("test");
            $result = $mesh->getWireguardIP();

            if (strpos($result, " ") === 0) {
                // print($result);
                $data = "No Container Found!";
                $this->response($data, 200);
            } else {
                $data = $result;
                // print($data);
                $this->response($data, 200);
            }
        } catch(Exception $e){
            $data = $result;
            $this->response($data, 403);
        }
    } else {
        $data = [
            "error" => "Bad request"
        ];
        $data = $this->json($data);
        $this->response($data, 400);
    }
};

