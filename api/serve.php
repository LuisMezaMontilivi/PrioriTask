<?php
require 'BdD.php';
/*
 Class: Server
*/
class Server {

   const APIKEY = "GuillemEsUnOient"; 

    /* Function: serve

    Funció inicial de la API
    */
    public function serve() {
      
      $uri = substr($_SERVER['REQUEST_URI'],4);
      $method = $_SERVER['REQUEST_METHOD'];

      if($method == "PUT"){
        switch($uri){
          case "/token/obtenir":
            if($_SERVER["HTTP_APIKEY"] == Server::APIKEY){
              echo $this->ProporcionarTokenInici();
            }
            else{
              header('HTTP/1.1 401 Unauthorized');
            }
            break;
          default:
            header('HTTP/1.1 404 Not Found');
            break;
        }
        
      }
      else{
        header('HTTP/1.1 405 Method Not Allowed');
      }
      
    }

    /* Function: GenerarTokenIdentificatiu
    
      Genera un token identificatiu a partir de les dades de l'usuari

      Parameters:
			  $id - id de l'usuari a realitzar el token
        $email - email de l'usuari a realitzar el token
    */
    function GenerarTokenIdentificatiu($id,$email){
      $idHash = hash("ripemd320",$id);//utilitizar mètodes diferents de diferents mides per evitar que es trobi fàcilment el mètode que utiltizem
      $emailHash = hash("sha256",$email);
      $tempsHash = hash("md5",time());
      $verificacio = hash("sha512",$idHash . $emailHash . $tempsHash);
      return $idHash . $emailHash . $tempsHash . $verificacio;
    }

    /* Function: TokenIdentificatiuGeneratServe
    
      Comprova que aquest token hagi estat generat amb el mateix algorisme del servidor

      Parameters:
			  $candidat - Token a comprovar que sigui o no generat per aquesta API
    */
    function TokenIdentificatiuGeneratServe($candidat){
      $id = substr($candidat,0,80);
      $email = substr($candidat,80,64);
      $temps = substr($candidat,144,32);
      $validacio = substr($candidat,176,128);
      return hash("sha512", $id . $email . $temps) == $validacio;
    }

    /* Function: GeneracioToken

      Genera un token vàlid per aquesta API
    */ 
    function GeneracioToken(){
      $ip = hash("md5",$this->ObtenirIP());//recuperem la IP des d'on ens fan la petició
      $data = hash("sha384",time());//obtenim el temps en format unix timestamp
      $validacio = $ip . $data;//fem una barreja entre tots dos per una part de validació
      return $ip . $data . hash("sha256",$validacio);
    }

    /* Function: TokenGeneratServe
    
      Comprova que aquest token hagi estat generat amb el mateix algorisme del servidor

      Parameters:
			  $candidat - Token a comprovar que sigui o no generat per aquesta API
    */
    function TokenGeneratServe($candidat){
      $ip = substr($candidat,0,32);//recuperem la part de la ip
      $data = substr($candidat,32,96);//idem data
      $validacio = substr($candidat,128,64);//la resta és validació
      return hash("sha256",$ip . $data) == $validacio;//comprovem que hagi seguit el nostre algorisme
    }

    /* Function: ObtenirIP

      Genera un token vàlid per aquesta API
    */
    function ObtenirIP(){
      if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
      } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
        return $_SERVER['REMOTE_ADDR']; 
      }
    }

    /* Function: ProporcionarTokenInici
    
      Retorna un Token no identificatiu
    */
    function ProporcionarTokenInici(){
      $obtenirToken = $this->GeneracioToken();
      BdD::connect();
      BdD::guardarTokenBD($obtenirToken);
      BdD::close();
      header('HTTP/1.1 201 OK');
      return $obtenirToken;
    }
    
    private function paths($url) {
      $uri = parse_url($url);
      return $uri['path'];
    }

  }



$server = new Server;
$server->serve();


?>