<?php

namespace Library;

class JsonResponse extends Response
{
    public function __construct($body, $status = 200, array $headers = [])
    {
        $body = [
            'result' => $status >= 200 && $status < 300 ? 'success' : 'fail',
            'data' => $body
        ];
        
        $body = json_encode($body);
        
        
        $headers['Content-type'] = 'application/json';
        parent::__construct($body, $status, $headers);
    }
}