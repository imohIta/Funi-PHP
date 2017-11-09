<?php
namespace core\libs;
use \PDO;
use \AppError;
/**
*
*
*/
defined('ACCESS') || AppError::exitApp();



class Database extends \FuniObject{

	/**
     * Allows multiple database connections
     */
    protected $_connections = array();

    /**
     * Tells the DB object which connection to use
     * setActiveConnection($connectionName) allows us to change this
     */
    protected $_activeConnection = '';

    /**
     * Data which has been prepared and then "saved for later"
     */
    protected $_queryCache = array();

    /**
     * Queries which have been executed and then "saved for later"
     */
    protected $_dataCache = array();

	 /**
     * Cache Engine to cache Database results
	 * Should be passed as a paramater to the consructor
     */
	protected $_cacheEngine;

	protected $_driver;


	protected $_name;





    /**
	 * @params Array of Db Configuration Options
     * Could Also Pass a cache object as argument in the future
     */
    public function __construct( Array $options = array(), $useConfigParams = false){

		global $registry;

		if(!empty($options)){

			# create new Database Connection with options
   		 	$this->newConnection($options);

		}

		# use config params to instantiate db if options array is empty and useConfigParams optionis set to true
		if(empty($options) && $useConfigParams){
			$this->newConnection(array());
		}


    }

    /**
     * Create a new database connection
	 * If active is true..sets this connection as the active connection
     * @param String database hostname
     * @param String database username
     * @param String database password
     * @param String database we are using
     * @return int the id of the new connection
     */
    public function newConnection( Array $options ){

		 global $registry;

		 $driver = isset($options['driver']) ?? 'mysql';
		 $host = isset($options['host']) ?? $registry->get('config')->get('dbHost');
		 $user = isset($options['user']) ?? $registry->get('config')->get('dbUser');
		 $pwd = isset($options['password']) ?? $registry->get('config')->get('dbPwd');
		 $dbName = isset($options['dbName']) ?? $registry->get('config')->get('dbName');
		 $active = isset($options['active']) ?? true;

		# set Db name
		$this->_name = $dbName;


		 //make Sure DbName is set
		 if(!$dbName){
			AppError::throwException('Database name not Set');
		 }

         $dsn = $driver . ":host=" . $host . ";dbname=" . $dbName . ";charset=utf8";

		 try{
			$this->_connections[$dbName] = new PDO($dsn, $user, $pwd );
			$this->_connections[$dbName]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    //$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		    $this->_connections[$dbName]->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , true);

		 }catch(PDOException $e){
			AppError::throwException('Database Error : ' . $e->getMessage(), '500');
		 }

		# if active is true..set this connection as the active connection
		if($active){
			$this->setActiveConnection($dbName);
		}

		# return DBObject to allow method chaining
		return $this;
    }

	/**
     * Set database
	 * @param String database hostname
     */
    public function setDatabase($obj, $dbName){

		try{
		   $this->_connections[$dbName] = $obj;
		   $obj->query('use `' . $dbName . '`');
		   $this->_connections[$dbName]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		   //$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		   $this->_connections[$dbName]->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , true);

		}catch(PDOException $e){
		   echo $e->getMessage();
		   die;
		}

		# set active connection
		$this->setActiveConnection($dbName);

		# set Db name
		$this->_name = $dbName;

		# return DBObject to allow method chaining
		return $this;

    }

    /**
     * Close the active connection
     * unset connection from connections array
	 * Only Use this when u want to totally destroy the connection
     */
    public function closeConnection(){
        $this->_connections[$this->_activeConnection] = null;
		unset($this->_connections[$this->_activeConnection]);
    }

    /**
     * Change which database connection is actively used for the next operation
     * @param int the new connection id
     * @return void
     */
    public function setActiveConnection( $conn ){
        $this->_activeConnection = $conn;
        $this->_driver = $this->_connections[$this->_activeConnection];
    }

	public function query( $queryStr, Array $parambinding = array(), $fetchMultiple = false ){
		//Use Active Connection to prepare PDO Statement
		$st = $this->_driver->prepare($queryStr);
		if(!empty($parambinding)){
			foreach($parambinding as $key => $value){
				if(ctype_digit($value)){
					$st->bindValue(':' . $key, $value, PDO::PARAM_INT);
				}else {
					$st->bindValue(':' . $key, $value, PDO::PARAM_STR);
				}
			}
		}
		if(!$fetchMultiple){
			return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
		}else{
			return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
		}
		//to be contd..

	}

	/**
	 * @author Imoh
	 * Insert records into the database
	 * @params tbl : table name to insert into
	 * @param Array params : a key-value pair array where the keys are database fields to be inserted
	 * @return bool
	 */

	public function insert($tbl, Array $params){

		# check if dbName exist
		$this->_checkTableExist($tbl);

		// setup some variables for Query, fields and values
		$fields  = "";
		$values = "";

		// populate them
		foreach ($params as $f => $v){
			$fields  .= " `$f`,";
			$values .= " :$f ,";
		}
		// remove our trailing ,
		$fields = substr($fields, 0, -1);
		// remove our trailing ,
		$values = substr($values, 0, -1);

		$queryStr = "INSERT INTO `$tbl` ({$fields}) VALUES({$values})";

		$st = $this->_driver->prepare($queryStr);
		//bind Parameters
		foreach($params as $field => $value){
			if(ctype_digit($value)){
				$st->bindValue(':' . $field, $value, PDO::PARAM_INT);
			}else {
				$st->bindValue(':' . $field, $value, PDO::PARAM_STR);
			}
		}


		try {
			$st->execute();
			$st = NULL;

			return true;
		}catch (PDOException $e){
			AppError::throwException($e->errorInfo, '500');
		}
	}


	/**
	 * @param $sp
	 * @param array $params
	 * @param array $bindings
	 * @return array
	 * @throws \Mf_Core\Upload\Exception
	 */
	public function bindFetch($queryStr, Array $params, Array $bindings){

		$st = $this->_driver->prepare($queryStr);

		# bind parameters
		if(!empty($params)){
			foreach($params as $key => $value){
				if(ctype_digit($value)){
					$st->bindValue(':' . $key, $value, PDO::PARAM_INT);
				}else {
					$st->bindValue(':' . $key, $value, PDO::PARAM_STR);
				}
			}
		}

		$st->execute();


		# for each column binding, bind column
		foreach($bindings as $binding){
			$st->bindColumn($binding, $$binding);
		}

		$st->fetch(PDO::FETCH_ASSOC);

		# build response
		$response = array();

		foreach($bindings as $binding){
			$response[$binding] = $$binding;
		}

		return $response;

	}



	/**
	 * @author Imoh
	 * Delete records from the database
	 * @param String tbl -  the tableName to remove rows from
	 * @param Array conditions -  an array of the field conditions and thier respective values
	 * @return bool
	 */
	public function delete( $tbl, Array $conditions ){

		# check if dbName exist
		$this->_checkTableExist($tbl);

		#check if conditions fieldnames are actual fieldnames in the tbl
		# @TODO


		#build query string
		$queryStr = 'delete from ' . $tbl;

		if(!empty($conditions)) {

			$queryStr .= ' where ';

			$counter = 1;
			foreach ( $conditions as $fieldName => $value ) {
				$queryStr .= $fieldName . ' = "' . $value . '"';
				if ( count($conditions) > 1 && $counter < count($conditions) ) {
					$queryStr .= ' and ';
				}
			}
		}

		//$queryStr = "DELETE FROM {$queryOptions['table']} WHERE {$queryOptions['condition']} {$limit}";

		#execute query
		if($this->_driver->exec($queryStr)){
			return true;
		}
		return false;
	}

	/*public function spDelete($sp, $params){

	}

    /**
     * Update records in the database
     * @param String tbl - the table name
     * @param array of changes field => value
     * @param String the condition
     * @return bool
     */
	public function update( $tbl, Array $changes, Array $conditions)
	{
		//update tableName set field1= value1, feild2 = value2 where cond1 = condValu1 and cond2 = conValue2
		# check if dbName exist
		$this->_checkTableExist($tbl);

		if(empty($changes)){
			AppError::throwException('update changes Array must not be Empty','500');
		}

//		if(empty($conditions)){
//			throw new \Mf_Core\Upload\Exception('Parameters Array must not be Empty');
//		}


		//if table option not provided
		if($tbl == ''){
			AppError::throwException('Table Name Option must not be empty');
		}

		//if Condition option not provided
		if(empty($changes)){
			AppError::throwException('Changes must not be empty when using update method');
		}


		//build query
		$queryStr = "UPDATE " . $tbl . " SET ";
		foreach( $changes as $field => $value )
		{
			$queryStr .= "`" . $field . "`='{$value}',";
		}

		// remove our trailing ,
		$queryStr = substr($queryStr, 0, -1);
		if(!empty($conditions)) {

			$queryStr .= ' where ';

			$counter = 1;
			foreach ( $conditions as $fieldName => $value ) {
				if(ctype_digit($value)) {
					$queryStr .= $fieldName . ' = ' . $value;
				}else{
					$queryStr .= $fieldName . ' = "' . $value . '"';
				}

				if ( count($conditions) > 1 && $counter < count($conditions) ) {
					$queryStr .= ' and ';
				}

				$counter++;
			}
		}


		if($this->_driver->exec( $queryStr )){
			return true;
		}
		return false;


	}


	/**
	 * @author Imoh
	 * Execute a query string
	 * @param String $queryStr : The query String
	 * @param Array $paramBinding : An Associative array of the parameters with thier corresponding values
	 * @param String $fetchType : flag to dertermine weather to return a single or multiple fetch from the DB
	 * @return PDOObject
	 */
	public function fetchCount( $queryStr, Array $parambinding = array() ){
		//Use Active Connection to prepare PDO Statement
		$st = $this->_driver->prepare($queryStr);
		if(!empty($parambinding)){
			foreach($parambinding as $key => $value){
				if(ctype_digit($value)){
					$st->bindValue(':' . $key, $value, PDO::PARAM_INT);
				}else {
					$st->bindValue(':' . $key, $value, PDO::PARAM_STR);
				}
			}
		}
		$st->execute();
		$st->bindColumn('count', $count);
		$st->fetch(PDO::FETCH_ASSOC);
		return $count;

	}


	/**
	 * @author Imoh
	 * build query string of a stored procedure
	 * @param Array Options option will contain query, table ( is Stored Procedure is not Used) , Array data
	 * @param sp ( Stored Procedure to call ) ..
	 * @return String queryStr
	 */
	private function _buildSpQueryString($sp, Array $params){

		if(empty($params)){
			throw new \Mf_Core\Upload\Exception('Parameters Array must not be Empty');
		}

		// setup some variables for Query, fields and values
		$values = "";

		// populate them
		foreach ($params as $f => $v){
			$values .= " :$f ,";
		}

		// remove our trailing ,
		$values = substr($values, 0, -1);


		if($sp == ''){
			throw new \Mf_Core\Upload\Exception('Stored Procedure must not be empty');
		}

		//Build and return query string
		return 'CALL ' . $sp . '( ' . $values . ' )';
	}



	/**
	 * @author Imoh
	 * Insert records into the database using a stored procedure
	 * @param Array Options option will contain query, table ( is Stored Procedure is not Used) , Array data
	 * @param sp ( Stored Procedure ) ..
	 *
	 */

	public function spInsert($sp, Array $params){

		//Build query string
		$queryStr = $this->_buildSpQueryString($sp, $params);


		$st = $this->_driver->prepare($queryStr);
		//bind Parameters
		foreach($params as $field => $value){
//			if(ctype_digit($value)){
//				$st->bindValue(':' . $field, $value, PDO::PARAM_INT);
//			}else {
//				$st->bindValue(':' . $field, $value, PDO::PARAM_STR);
//			}
			$st->bindValue(':' . $field, $value);
		}

		try {

			if($st->execute()){
				return true;
			}

			return false;

		}catch (PDOException $e){
			var_dump($e->errorInfo);
		}

	}

	/**
	 * @author Imoh
	 * Queries records from the database using a stored procedure
	 * @param Array Options option will contain query, table ( is Stored Procedure is not Used) , Array data
	 * @param String $sp ( Stored Procedure ) ..
	 * @param String $fetchType : flag to dertermine weather to return a single or multiple fetch from the DB
	 * @return PDOObject
	 */

	public function spQuery($sp, Array $params, $fetchType = 'single'){

		//Build query string
		$queryStr = $this->_buildSpQueryString($sp, $params);


		$st = $this->_driver->prepare($queryStr);
		//bind Parameters
		foreach($params as $field => $value){
			if(ctype_digit($value)){
				$st->bindValue(':' . $field, $value, PDO::PARAM_INT);
			}else {
				$st->bindValue(':' . $field, $value, PDO::PARAM_STR);
			}
		}

		if($fetchType == 'single'){
			return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : null;
		}else{
			return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : null;
		}

	}

	/**
	 * @author Imoh
	 * Queries records from the database using a stored procedure
	 * @param Array Options option will contain query, table ( is Stored Procedure is not Used) , Array data
	 * @param String $sp ( Stored Procedure ) ..
	 * @return Int
	 */

	public function spfetchCount($sp, Array $params){

		//Build query string
		$queryStr = $this->_buildSpQueryString($sp, $params);


		$st = $this->_driver->prepare($queryStr);
		//bind Parameters
		foreach($params as $field => $value){
			if(ctype_digit($value)){
				$st->bindValue(':' . $field, $value, PDO::PARAM_INT);
			}else {
				$st->bindValue(':' . $field, $value, PDO::PARAM_STR);
			}
		}

		$st->execute();

		$st->bindColumn('count', $count);
		$st->fetch(PDO::FETCH_ASSOC);
		return $count;

	}


	/**
	 * @author Imoh
	 * Queries records and fetches selected fields from the database using a stored procedure
	 * @param Array Options option will contain query, table ( is Stored Procedure is not Used) , Array data
	 * @param String $sp ( Stored Procedure ) ..
	 * @param Array $bindings : columns to fetch from the table
	 * @return Int
	 */

	public function spBindFetch($sp, Array $params, Array $bindings){

		//Build query string
		$queryStr = $this->_buildSpQueryString($sp, $params);


		$st = $this->_driver->prepare($queryStr);
		//bind Parameters
		foreach($params as $field => $value){
			if(ctype_digit($value)){
				$st->bindValue(':' . $field, $value, PDO::PARAM_INT);
			}else {
				$st->bindValue(':' . $field, $value, PDO::PARAM_STR);
			}
		}

		$st->execute();

		# for each column binding, bind column
		foreach($bindings as $binding){
			$st->bindColumn($binding, $$binding);
		}

		$st->fetch(PDO::FETCH_ASSOC);

		# build response
		$response = array();

		foreach($bindings as $binding){
			$response[$binding] = $$binding;
		}

		return $response;



	}


	/**
	 * @author Imoh
	 * Queries records and fetches selected fields from the database using a stored procedure
	 * @param Array Options option will contain query, table ( is Stored Procedure is not Used) , Array data
	 * @param String $sp ( Stored Procedure ) ..
	 * @param Array $bindings : columns to fetch from the table
	 * @return Int
	 */

	public function spFetchColumn($sp, Array $params){

		//Build query string
		$queryStr = $this->_buildSpQueryString($sp, $params);


		$st = $this->_driver->prepare($queryStr);
		//bind Parameters
		foreach($params as $field => $value){
			$st->bindValue( ':' .$field, $value);
		}

		$st->execute();

		# return response
		return $st->fetchColumn();

	}


	/**
	 * @author Imoh
	 * Store some data in a cache for later
	 * @param array the data
	 * @return int the pointed to the array in the data cache
	 */
	public function cacheData( $key, $data )
	{
		//@todo Use phpFastCache
	}


	/*
	 * @author Imoh
	 * check if a table name exist in the database
	 * @param tbl - the table name
	 * @return void
	 */
	private function _checkTableExist($tbl){

		if($tbl == ''){
			throw new \Exception('Table Name cannot be empty');
		}

		#check if table name exist in the database
		$query = "SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = '" . $this->_name . "' AND table_name = '" . $tbl . "'";
		$st = $this->_driver->prepare($query);
		$st->execute();
		$st->bindColumn('count', $count);
		$st->fetch(PDO::FETCH_ASSOC);

		if ($count == 0){
			throw new \Exception('Table ( ' . $tbl . ' ) does not exist in the database ' . $this->_name);
		}else{
			return true;
		}
	}


}
