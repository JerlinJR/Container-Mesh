<?php

//TODO: Check for authentication before accessing the file

${basename(__FILE__, '.php')} = function(){
    if($this->get_request_method() == "POST"){
        try {
            $test = "test";
            $mesh = new MeshCtl($test);
            $result = $mesh->listContainers();

            if (strpos($result, "No") === 0) {
                // print($result);
                $data = $result;
                $this->response($data, 200);
            } else {
                $data = $result;
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

