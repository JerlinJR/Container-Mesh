<?php

${basename(__FILE__, '.php')} = function(){
    if($this->get_request_method() == "POST" and !empty($this->_request['public_key']) and !empty($this->_request['email'])){ //No authentication required to add peer to wg
    // if($this->get_request_method() == "POST" and $this->isAuthenticated() and !empty($this->_request['public_key']) and !empty($this->_request['email'])){
        try{
            $device = 'wg0';
            if(isset($this->_request['device'])){
                $device = $this->_request['device'];
            }
            $wg = new Wireguard($device);
            $data = [
                "result" => $wg->addPeer($this->_request['public_key'], $this->_request['email'], isset($this->_request['reserved']) ? boolval($this->_request['reserved']):false, isset($this->_request['ip']) ? $this->_request['ip'] : null),
            ];
            $data = $this->json($data);
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