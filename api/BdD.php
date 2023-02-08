<?php
class BdD {
	
    private static $connection;

	//La base de dades està posada de manera fixa al codi
        //Per treballar en local cal canviar user i contra. Per a realease:
            //  self::$connection = new PDO("mysql:host=127.0.0.1;dbname=PrioriTaskBD", "adminer", "Bhun@89ble.oient");
    public static function connect() {
        try{
            self::$connection = new PDO("mysql:host=localhost;dbname=PrioriTaskBD", "usuari", "1234");
        }
        catch(PDOException $e){
           self::$connection = null;
            return $e->getMessage();
        }
      
    }

	//Tancar conexió a la BdD
    public static function close() {
        self::$connection = null;
    }
	
	//Funcionalitats a la BdD

    public static function loginBD($email, $contrasenya){
        $output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                SELECT  * from Usuari u 
                where email = :email && contrasenya = :contrasenya
                ;
                "
            );
            $query->bindParam(':email', $email);
            $query->bindParam(':contrasenya', $contrasenya);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $outcome = $query->fetchAll();
            if(count($outcome)>0){
                $output[0] = $outcome[0]["id_usuari"];
                $output[1] = $outcome[0]["email"];
            }
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            $output = $e->getMessage();
        }
        

        return $output;
    }


    public static function canviContrasenyaBD($token, $contrasenya){
        $output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                Update Usuari 
                set contrasenya = :contrasenya
                WHERE token = :token
                ;
                "
            );
            $query->bindParam(':contrasenya', $contrasenya);
            $query->bindParam(':token', $token);
            $outcome = $query->execute();
            $output =  $query->rowCount();
           
         }
         catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $output;

    }



    /* Function: validaTokenBD

        A partir del token donat, consulta a la BdD si existeix un usuari amb el determinat token

        Parameters:
            $tokenAValidar - token a comprovar

        Returns: rol del usuari o bé un bool de false en cas de no trobar-lo


    */
    public static function validaTokenBD($tokenAValidar){
        $output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                Select rol from Usuari WHERE token = :token ;
                ;
                "
            );
            $query->bindParam(':token', $tokenAValidar);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $outcome = $query->fetchAll();
            if(count($outcome)== 1){
                $output = $outcome[0]["rol"];
            }
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            $output = $e->getMessage();
        }
        return $output;
    }
        /* Function: consultaUsuarisBD

        Retorna un llistat de tots els usuaris de l'aplicació

        Returns: array d'usuaris de l'aplicació


        */
    public static function consultaUsuarisBD(){

        $output = false;
        try{
            $query = (self::$connection)->prepare(
                "
                SELECT id_usuari , nom, email , rol, data_alta , data_baixa , data_ultima_peticio  from Usuari u ;
                ;
                "
            );
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $output = $query->fetchAll();

        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            $output = $e->getMessage();
        }
        return $output;

    }


    /* Function: consultaTecnicsBD

        Retorna un llistat de tots els tècnics de l'aplicació amb el seu nom, email i id

        Returns: array de tècnics de l'aplicació amb el seu nom, email i id


        */
        public static function consultaTecnicsBD(){

            $output = false;
            try{
                $query = (self::$connection)->prepare(
                    "
                    SELECT id_usuari , nom , email  from Usuari u WHERE rol ='t';
                    ;
                    "
                );
                $query->execute();
                $query->setFetchMode(PDO::FETCH_ASSOC);
                $output = $query->fetchAll();
    
            }
            catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
                $output = $e->getMessage();
            }
            return $output;
    
        }


    }

   

?>