<?php

namespace Library;

class Response 
{
    protected $body;
    
    protected $status;
    
    protected $headers;
    
    public function __construct($body, $status = 200, array $headers = [])
    {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
    }
    
    public function send()
    {
        http_response_code($this->status);
        
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
    }
    
    public function __toString()
    {
        return (string) $this->body;
    }
}