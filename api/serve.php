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
          case "/usuari/iniciar":
            echo $this->login();
            break;
          case "/usuari/contrasenya":
            echo $this->canviaContrasenya();
            break;
          case "/usuari/llistat":
            var_dump( $this->consultaUsuaris());
            break;
          case "/usuari/llistat_tecnics":
            var_dump($this->consultaTecnics());
            break;
          case "/usuari/alta":
            var_dump($this->creaUsuari());
            break;
          case "/usuari/modificar":
            var_dump($this->modificaUsuari());
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