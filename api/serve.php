<?php
require 'BdD.php';

class Server {

    public function serve() {
      
        echo "Pastanaga";
      
    }
    
    private function paths($url) {
      $uri = parse_url($url);
      return $uri['path'];
    }

  }



$server = new Server;
$server->serve();


?>