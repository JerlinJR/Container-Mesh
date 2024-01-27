<?php

${basename(__FILE__, '.php')} = function(){
    if($this->get_request_method() == "POST" and $this->isAuthenticated() and !empty($this->_request['ip']) and !empty($this->_request['email'])){
        try{
            $device = 'wg0';
            if(isset($this->_request['device'])){
                $device = $this->_request['device'];
            }
            $wg = new Wireguard($device);
            $data = $wg->reserve($this->_request['ip'], $this->_request['email']);
            $data = $this->json(['result'=>$data]);
            $this->response($data, 200);
        } catch(Exception $e){
            $data = [
                "error" => $e->getMessage()
            ];
            $data = $this->json($data);
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