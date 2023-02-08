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
          if($uri == '/API/usuari/llistat'){
            var_dump( $this->consultaUsuaris());
          }
          if($uri == '/API/usuari/llistat_tecnics'){
            var_dump($this->consultaTecnics());
          }
          if($uri == '/API/usuari/alta'){
            var_dump($this->creaUsuari());
          }
          if($uri == '/API/usuari/modificar'){
            var_dump($this->modificaUsuari());
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


        /* Function: canviaContrasenya()
          
          A partir dels Headers rebuts, validació de token rebut, canvi de contrassenya a la BdD al usuari que té el token, retorn d'OK o Unauthorised

           Returns: boolea resultant del procés + headers corresponents: 200 OK o 401 Unauthorised

        */
        private function canviaContrasenya(){
          $token = $_SERVER['HTTP_TOKEN']; //Obtenim el token per headers
          $contrasenya = $_SERVER['HTTP_CONTRASENYA'];
          echo "Ha entrat a la funció";
          //Validació del token actual
          if($this->validaToken() != false)
          {
            BdD::connect();
            $output = BdD::canviContrasenyaBD($token, $contrasenya);
            if($output == 1)
            {
              //Funció de inserció de darrera petició a la BdD
              header('HTTP/1.1 200 OK');
              }
              else{
                  header('HTTP/1.1 400 Bad Request'); 
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


        /* Function: validaToken

        A partir dels headers rebuts, validació del token contra la BdD, retorn del rol del usuari o bool de false

        Parameters:
          $tokenAValidar - string amb el token que volem validar contra la BdD

        Returns: rol del usuari si el token és vàlid o bool false en cas contrari
        */
        private function validaToken($tokenAValidar){
          BdD::connect();
          $output = BdD::validaTokenBD($tokenAValidar);
          BdD::close();
          return $output;
        }

         /* Function: consultaUsuaris()
          
           A partir dels Headers rebuts, validació de token rebut, i retorn d'array d'arrays amb dades dels usuaris de l'aplicació, amb excepció dels camps no pertinents
           
           Returns: array d'arrays amb les dades dels usuaris(id, nom, email, rol, dates d'alta, baixa i ultima petició) + headers corresponents: 200 OK o 401 Unauthorised

        */

        private function consultaUsuaris(){
          $token = $_SERVER['HTTP_TOKEN']; //Obtenim el token per headers
          $rol = $this->validaToken($token);
          if($rol == 'a')
          {
            BdD::connect();
            $output = BdD::consultaUsuarisBD();
            BdD::close();
          }
          else
          {
            header('HTTP/1.1 401 Unauthorised');
            $output = "no és usuari correcte";
          }
          return $output;
        }
        
         /* Function: consultaUsuaris()
          
           A partir dels Headers rebuts, validació de token rebut, i retorn d'array d'array de les dades de id, nom i mail dels tècnics de l'aplicació
           
           Returns: array d'arrays amb les dades d'email, nom i mail  dels tècnics + headers corresponents: 200 OK o 401 Unauthorised

        */
        private function consultaTecnics(){
          $token = $_SERVER['HTTP_TOKEN']; //Obtenim el token per headers
          $rol = $this->validaToken($token);
          if($rol == 'a' || $rol == 'g')
          {
            BdD::connect();
            $output = BdD::consultaTecnicsBD();
            BdD::close();
          }
          else
          {
            header('HTTP/1.1 401 Unauthorised');
            $output= "no és usuari correcte";
          }
          return $output;
        }

        /* Function: creaUsuari
          
           A partir dels Headers rebuts, validació de token rebut, inserció del nou usuari amb les dades rebudes i retorn de booleà de l'execució de la query
           
           Returns: bool resultat de l'execució

        */  
        private function creaUsuari(){
          $token = $_SERVER['HTTP_TOKEN']; //Obtenim el token per headers
          $rol = $this->validaToken($token);
          $infoUsuari = json_decode($_SERVER['HTTP_INFORMACIOUSUARI'],true);
          if($rol =='a')
          {
            BdD::connect();
            $output = BdD::creaUsuariBD($infoUsuari);
            BdD::close();
          }
          else
          {
            header('HTTP/1.1 401 Unauthorised');
            $output= "no és usuari correcte";
          }
           return $output;
        }


        private function modificaUsuari(){
          $token = $_SERVER['HTTP_TOKEN']; //Obtenim el token per headers
          $rol = $this->validaToken($token);
          $infoUsuari = json_decode($_SERVER['HTTP_INFORMACIOUSUARI'],true);
          if($rol =='a')
          {
            BdD::connect();
            $output = BdD::modificaUsuariBD($infoUsuari);
            BdD::close();
          }
          else
          {
            header('HTTP/1.1 401 Unauthorised');
            $output= "no és usuari correcte";
          }
           return $output;
        }



  
    }

  



$server = new Server;
$server->serve();


?>