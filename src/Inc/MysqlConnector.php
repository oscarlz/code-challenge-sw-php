<?php

namespace App\Inc;

class MysqlConnector
{
    protected $connection = null;
    protected $query = null;

    public function __construct() 
    {
        try {

            $this->connection = new \MySQLi($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
            $this->connection->set_charset($_ENV['DB_CHARSET']);
            
        }catch (\Throwable $th){
            //todo: log this error
            exit('Error connecting to database'); 
        }
    }

    /**
     * Run sql query using optional params.
     * 
     * @param string $sql
     * @param array $args Optional, parameters to use as bind params
     * @return mixed False on failure. mysqli_resut on success that return data. True on other successfull queries
     */
    public function run($sql, $args = [])
    {
        $this->query = $this->connection->prepare($sql);
        
        if(count($args) > 0){
            $getArgsTypesString = $this->getBindParamsTypes($args);
            $this->query->bind_param($getArgsTypesString, ...$args);
        }
        
        $this->query->execute();
        
        return $this->query;
    }

    public function lastInsertID() 
    {
    	return $this->connection->insert_id;
    }

    public function affectedRows() 
    {
		return $this->query->affected_rows;
	}

    /**
     * Return the first letter of the values in $args array. This function is used when using mysqli bind_params.
     * 
     * @param array $args
     * @return string
     */    
	private function getBindParamsTypes($args)
    {
        $typesString = '';

        foreach($args as $arg){
            
            if (is_string($arg)) $typesString .= 's';
            if (is_float($arg)) $typesString .= 'd';
            if (is_int($arg)) $typesString .= 'i';
        }

	    return $typesString;
	}    
}