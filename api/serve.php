<?php
require 'BdD.php';

class Server {

    public function serve() {
      $uri = $_SERVER['REQUEST_URI'];
      $method = $_SERVER['REQUEST_METHOD'];
        //echo $uri;

        if($method == 'PUT'){
          if($uri == '/API/usuari/iniciar'){
            echo $this->login();
          }
          if($uri == '/API/usuari/contrasenya'){
            echo $this->canviaContrasenya();
          }
        }
        else{
          echo "El mètode no era Put";
        }
      
    }
    
    private function paths($url) {
      $uri = parse_url($url);
      return $uri['path'];
    }

    /* Function: login

    A partir dels Headers rebuts, validació de token inicial, login de l'usuari contra la BdD, 
    creació del nou token a partir de les dades del usuari + token prèvi, inserció de nou token a la BdD, inserció de hora de la darrera petició de l'usuari a la BdD

    Returns: boolea false si el procés no és fructífer/ array amb id,mail + headers corresponents: 200 OK o 401 Unauthorised

    */
    private function login(){
      $email = $_SERVER['HTTP_EMAIL'];
      $contrasenya = $_SERVER['HTTP_CONTRASENYA'];
      $token = $_SERVER['HTTP_TOKEN']; //Obtenim el token per headers
         //Funció de validació
         if($token){
       
          BdD::connect();
          $output = BdD::loginBD($email, $contrasenya);
          if($output != false){
            //Funció de generació i insert del nou token a la BDD. El resultat d'aquesta inserció serà el nostre output
            //Funció de inserió de darrera petició a la BdD
            
            header('HTTP/1.1 200 OK');
            }
          else{
            header('HTTP/1.1 401 Unauthorised');
            $output= "no és usuari correcte";
            }
          // Tanquem la bdd
          BdD::close();
         }
          return $output;
        }


        /* Function: canviaContrasenya(){
          
          A partir dels Headers rebuts, validació de token rebut, canvi de contrassenya a la BdD al usuari que té el token, retorn d'OK o Unauthorised

           Returns: boolea resultant del procés + headers corresponents: 200 OK o 401 Unauthorised

        */
        private function canviaContrasenya(){
          $token = $_SERVER['HTTP_TOKEN']; //Obtenim el token per headers
          $contrasenya = $_SERVER['HTTP_CONTRASENYA'];
          echo "Ha entrat a la funció";
          //Validació del token actual
          if($token)
          {
          BdD::connect();
          echo "Ha entrat a token i ha conectat";
            $output = BdD::canviContrasenyaBD($token, $contrasenya);
            if($output == 1)
            {
              //Funció de inserió de darrera petició a la BdD
              header('HTTP/1.1 200 OK');
              }
              else{
                //  header('HTTP/1.1 304 Not Modified'); //Preguntar a Carles
                $output = "Token Incorrecte";
                }
               // Tanquem la bdd
            BdD::close();
          } 
          else{
            header('HTTP/1.1 401 Unauthorised');
            $output = "Token Incorrecte";
            }
          echo $output;
          return $output;


        }





    

    }

  



$server = new Server;
$server->serve();


?>