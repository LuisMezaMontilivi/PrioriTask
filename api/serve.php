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
            $this->ProporcionarTokenInici();            
            break;
          case "/usuari/iniciar":
            echo $this->login();
            break;
          case "/usuari/contrasenya":
            echo $this->canviaContrasenya();
            $this->updateDataUltimaPeticio();
            break;
          case "/usuari/llistat":
            echo $this->consultaUsuaris();
            $this->updateDataUltimaPeticio();
            break;
          case "/usuari/llistat_tecnics":
            echo $this->consultaTecnics();
            $this->updateDataUltimaPeticio();
            break;
          case "/usuari/alta":
            var_dump($this->creaUsuari());
            $this->updateDataUltimaPeticio();
            break;
          case "/usuari/modificar":
            var_dump($this->modificaUsuari());
            $this->updateDataUltimaPeticio();
          case "/usuari/data":
            $this->RecuperarDataSetUsuaris();
            $this->updateDataUltimaPeticio();
            break;
          case "/tasca/crear":
            $this->CrearNovaTasca();
            $this->updateDataUltimaPeticio();
            break;
          case "/tasca/modificar":
            $this->ModificarTasca();
            $this->updateDataUltimaPeticio();
            break;
          case "/tasca/llistat":
            $this->ObtenirLlistatTasques();
            $this->updateDataUltimaPeticio();
            break;
          case "/tasca/data":
            $this->RecuperarDataSetTasques();
            $this->updateDataUltimaPeticio();
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

    function ObtenirLlistatTasques(){
      $rolToken = $this->validaToken($_SERVER["HTTP_TOKEN"]);
      if(isset($rolToken)){
        BdD::connect();
        $llistat = json_encode(BdD::recuperarLlistatTasquesBD($_SERVER["HTTP_TOKEN"]));
        echo $llistat;
        BdD::close();
      }
    }

    /* Function: ModificarTasca
    
      Modifica una tasca ja existent si l'usuari actual té permisos de modificació (admin o gestor que l'ha creat)
    */
    function ModificarTasca(){
      $rolToken = $this->validaToken($_SERVER["HTTP_TOKEN"]);//primer hem d'obtenir el rol del token 
      if(isset($rolToken)){//si el token existeix i no es admin
        $tasca = json_decode($_SERVER["HTTP_TASCA"],true);
        BdD::connect();
        $estat = BdD::relacionatsTascaBD($tasca["id"]);
        BdD::close();
        if($estat["token_gestor"]==$_SERVER["HTTP_TOKEN"]){//podem editar tots els camps
          BdD::connect();
          BdD::modificarTascaGestorBD($tasca,$estat);
          BdD::close();
        }
        elseif($estat["token_tecnic"]==$_SERVER["HTTP_TOKEN"]){//només es pot editar els camps comentari i estat
          BdD::connect();
          BdD::modificarTascaTecnicBD($tasca,$estat);
          BdD::close();
        }
        else{
          header('HTTP/1.1 403 Forbidden');
        }
      }
    }

    /* Function: CrearNovaTasca

      Crea una nova tasca creada amb l'usuari actual
     */
    function CrearNovaTasca(){
      $rolToken = $this->validaToken($_SERVER["HTTP_TOKEN"]);
      $usuariValid = $rolToken == 'a' || $rolToken == 'g';//cridar al mètode per veure segons el token rebut si és o no vàlid i és gestor/admin
      if($usuariValid){
        $tasca = json_decode($_SERVER["HTTP_TASCA"],true);
        BdD::connect();
        BdD::guardarTascaBD($tasca);
        BdD::close();
        header('HTTP/1.1 201 Created');
      }
      else{
        header('HTTP/1.1 403 Forbidden');
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
      if($_SERVER["HTTP_APIKEY"] == Server::APIKEY){
        $obtenirToken = $this->GeneracioToken();
        BdD::connect();
        BdD::guardarTokenBD($obtenirToken);
        BdD::close();
        header('HTTP/1.1 201 Created');
        echo $obtenirToken;
      }
      else{
        header('HTTP/1.1 401 Unauthorized');
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
    private function login()
    {
      $token = $_SERVER['HTTP_TOKEN']; //Obtenim el token per headers
      $maininfo = json_decode($_SERVER['HTTP_MAININFO'],true);
      $email = $maininfo["usuari"];
      $contrasenya = $maininfo["contrasenyaEncriptada"];
     
     
      if($this->TokenGeneratServe($token))
      {
        BdD::connect();
        if(BdD::existeixTokenInicialBD($token))
        {
          //Funció de login amb les dades de l'usuari
          
          $output = BdD::loginBD($email, $contrasenya); //Ens retornarà un array de 2 posicions on 0 és id i 1 email
          if($output != false)
          {
             // Crear token
            $tokenIdentificatiu = $this->GenerarTokenIdentificatiu($output[0], $output[1]);
            $output = BdD::guardarTokenIdentificatiuBD($tokenIdentificatiu, $output[0]);
            if($output == 1)
            {
              $output = $tokenIdentificatiu;
              header('HTTP/1.1 200 OK');
            }
           
          }
          else
          {
            header('HTTP/1.1 401 Unauthorised');
            $output= "no és usuari correcte";
          }
          BdD::close();
        }
        else
          {
            header('HTTP/1.1 401 Unauthorised');
            $output= "no és usuari correcte";
          }
      }
      return $output;
    }


        /* Function: canviaContrasenya()
          
          A partir dels Headers rebuts, validació de token rebut, canvi de contrassenya a la BdD al usuari que té el token, retorn d'OK o Unauthorised

           Returns: boolea resultant del procés + headers corresponents: 200 OK o 401 Unauthorised

        */
        private function canviaContrasenya(){
          $maininfo = json_decode($_SERVER['HTTP_MAININFO'],true);
          $contrasenya = $maininfo["contrasenyaEncriptada"];
          $token = $_SERVER['HTTP_TOKEN']; //Obtenim el token per headers
          //Validació del token actual
          if($this->validaToken($token) != false)
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
            $output = json_encode($output);
          }
          else
          {
            header('HTTP/1.1 401 Unauthorised');
            $output = "no és usuari correcte";
          }
          return $output;
        }
        
         /* Function: consultaTècnics()
          
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
            $output = json_encode($output);
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

         /* Function: modificaUsuari
          
           A partir de les dades rebudes , inserció del nou usuari amb les dades rebudes i retorn de booleà de l'execució de la query
           
           Returns: bool true resultat de l'execució o missatge d'error si no s'és administrador

        */  
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

        /* Function: updateDataUltimaPeticio
          
            A partir del token de l'usuari, actualitzar la data de la darrera petició al moment de realització
           
           Returns: bool resultat de l'execució

        */  
        private function updateDataUltimaPeticio(){
          
          try{
            BdD::connect();
            $output = BdD::updateDataUltimaPeticioBD($token);
            BdD::close();
          }
          catch(Exception $e)
          { $output = false;}
          return $output;      
        }

        /* Function: RecuperarDataSetTasques

          Retornarà les estadístiques de les tasques de l'últim mes a admin
        */
        private function RecuperarDataSetTasques(){
          $rolToken = $this->validaToken($_SERVER["HTTP_TOKEN"]);
          if($rolToken == "a"){
            BdD::connect();
            $tasques = json_encode(BdD::recuperarEstatTasquesBD());
            echo $tasques;
            BdD::close();
          }
          else{
            header("HTTP/1.1 403 Forbidden");
          }
        }

        /* Function: RecuperarDataSetUsuaris

          Retornarà les estadístiques d'activitat dels usuaris
        */
        private function RecuperarDataSetUsuaris(){
          $rolToken = $this->validaToken($_SERVER["HTTP_TOKEN"]);
          if($rolToken == "a"){
            BdD::connect();
            $usuaris = json_encode(BdD::recuperarEstatUsuarisBD());
            echo $usuaris;
            BdD::close();
          }
          else{
            header("HTTP/1.1 403 Forbidden");
          }
        }
  
    }

  



$server = new Server;
$server->serve();


?>