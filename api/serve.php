<?php
require 'BdD.php';

class Server {

    public function serve() {
      
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        
        $paths = explode('/', $this->paths($uri));
        array_shift($paths); // Saltem els valors de la URL
        if (!is_null($paths))
          array_shift($paths);
          
        $resource = "";
        if (!is_null($paths))
          $resource = array_shift($paths);
		
      
    }
    
    private function paths($url) {
      $uri = parse_url($url);
      return $uri['path'];
    }

  }



$server = new Server;
$server->serve();


?>