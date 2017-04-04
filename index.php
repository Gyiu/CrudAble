<?php

include("interface.php");

class Database {
    private $_config = array(
        'hostname' => 'TRA-NB-07\SQLEXPRESS',
        'username' => 'a',
        'password' => 'q',
        'database' => 'namespace'
    );

    private $_connection = FALSE;
    private $_query = FALSE;

    public function connect()
    {
        $config = $this->_config;

        $connect = mssql_connect($config['hostname'], $config['username'], $config['password']);

        if($connect){
            $this->_connection = mssql_select_db($config['database'], $connect);

            return $this->_connection;
        }else{
            exit('Mohon maaf, Server sedang mengalami masalah');
        }
    }

    public function query($query, $params = array()){
		$segments = explode('?', $query);

		if (count($params) >= count($segments)) {
			$params = array_slice($params, 0, count($segments)-1);
		}

		$result = $segments[0];
		$i = 0;
		foreach ($params as $bind)
		{
			$result .= "'".$bind."'";
			$result .= $segments[++$i];
		}

        if($this->_connection === FALSE){
            $this->connect();
        }

        $this->_query = mssql_query($result);
    }

    public function result()
    {
        $query = $this->_query;
        $records = array();

        if($query === FALSE){
            exit('Tidak dapat menjalankan perintah ini');
        }

        while($record = mssql_fetch_array($query)){
            $records[] = $record;
        }

        return $records;
    }
}

class CRUD implements \App\Interfaces\CrudAble
{
    public $db;
    public $table_name;

    function __construct($table_name)
    {
        $this->db = new Database;

        $this->db->connect();

        $this->table = $table_name;

    }
    
    public function create(array $data)
    {
        foreach($data as $key => $value){
            $fields[] = $key;
            $values[] = '?';
        }

        $fields = implode(',', $fields);
        $values = implode(',', $values);
        
        $this->db->query("INSERT INTO $this->table ($fields) values ($values)", $data);
    }

    public function read($id)
    {
        $this->db->query("SELECT * FROM $this->table WHERE id = $id");

        $output = $this->db->result();

        echo json_encode($output);
    }

    public function update($id, array $data)
    {
        $set_value = array();

        foreach($data as $key => $value){
            $set_value[] = "$key = '$value'";
        }

        $set_value = implode(',', $set_value);

        $this->db->query("UPDATE $this->table set $set_value WHERE ID = $id", $data);
    }

    public function delete($id)
    {
        $this->db->query("DELETE $this->table WHERE id = $id");
    }
}

$one = new CRUD('pegawai');

$one->create(array('nama'=> 'Dony', 'alamat'=> 'Brangkal', 'nip'=>'11'));
$one->update(2, array('nama'=> 'Dony Aja'));
$one->read(2);
$one->delete(2);