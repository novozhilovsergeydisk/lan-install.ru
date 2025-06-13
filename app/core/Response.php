<?php

class Response {
    private $statusCode = 200;
    private $headers = [];
    private $content = '';
    
    public function setStatusCode($code) {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
        return $this;
    }

    // ✅ Добавьте этот метод:
    public function getHeaders() {
        return $this->headers;
    }
    
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }
    
    public function getContent() {
        return $this->content;
    }
    
    public function send() {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        echo $this->content;
    }
    
    public function json($data) {
        $this->setHeader('Content-Type', 'application/json');
        $this->setContent(json_encode($data));
        $this->send();
    }
}
