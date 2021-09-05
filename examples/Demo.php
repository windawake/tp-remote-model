<?php 
namespace TpRemoteModel\Examples;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Demo  {
    protected $listenHost = '127.0.0.1';
    protected $listenPort = '18080';

    public function getRemoteUrl()
    {
        return 'http://'.$this->listenHost.':'.$this->listenPort;
    }

    public function getListenPort()
    {
        return $this->listenPort;
    }

    public function getGuzzleHttpContent($requestMethod, $table, $options, $id = 0)
    {
        
        $url = $this->getRemoteUrl();
        $uri = 'index.php';
    
        $client = new Client([
            'base_uri'        => $url,
            'timeout'         => 10,  // 请求超时时长
            'allow_redirects' => false,                  // 禁止重定向
            'http_errors'     => true,
        ]);
        $request = new Request($requestMethod, $uri);
        
        $response = $client->send($request, [
            'json' => [
                'options' => $options,
            ],
            'query' => [
                'table' => $table,
                'id' => $id
            ],
        ]);
        $content = $response->getBody()->getContents();
    
        return json_decode($content, true);
    }
}


return [
    'remote_url' => 'http://127.0.0.1:18000'
];