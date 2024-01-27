<?php

//TODO:Check for authentication before accessing the file

${basename(__FILE__, '.php')} = function(){
    if($this->get_request_method() == "POST" and !empty($this->_request['name'])){
        try {
            $mesh = new MeshCtl($this->_request['name']);
            $result = $mesh->runContainer();

            if (strpos($result, "ERROR") === 0) {
                // print($result);
                $data = $result;
                $this->response($data, 409);
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

