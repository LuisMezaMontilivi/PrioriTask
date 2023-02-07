<?php
class BdD {
	
    private static $connection;

	//La base de dades està posada de manera fixa al codi
    public static function connect() {
        self::$connection = new PDO("mysql:host=localhost;dbname=PrioriTaskBD", "adminer", "1234");
    }

	//Tancar conexió a la BdD
    public static function close() {
        self::$connection = null;
    }
	
	//Funcionalitats a la BdD
    
    /* Function: guardarTokenBD
    
    */
    public static function guardarTokenBD($token){
		$guardat = true;
		try{
			$consulta = (BdD::$connection)->prepare("
				INSERT INTO TokenGenerat (Token)
				VALUES (:token);			
			");
			$consulta->bindParam('token',$token);
			$consulta->execute();
		}
		catch(PDOException $e){
			$guardat = false;
		}
		return $guardat;
	}
}

?>