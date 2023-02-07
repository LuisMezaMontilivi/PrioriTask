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
}
?>