<?php

class Model
{
	protected $_dbh = null;
	protected $_table = "";
	
	public function __construct()
	{
		$settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);
        $this->_dbh = Database::instance()->getConnection(
            sprintf(
                "%s:host=%s;dbname=%s",
                $settings['database']['driver'],
                $settings['database']['host'],
                $settings['database']['dbname']
            ),
            $settings['database']['user'],
            $settings['database']['password']
        );
		$this->init();
	}
	
	public function init()
	{
		
	}

    /**
     * @param $table
     */
    protected function _setTable($table)
	{
		$this->_table = $table;
	}
	
	public function fetchOne($id)
	{
		$sql = "select * from {$this->_table}";
		$sql .= " where id = '{$id}'";

		$statement = $this->_dbh->prepare($sql);
		$statement->execute(array($id));
		
		return $statement->fetch(PDO::FETCH_OBJ);
	}

    /**
     * @param array $data
     * @return bool|string
     */
    public function save($data = array())
	{
		$sql = '';
		
		$values = array();
		
		if (array_key_exists('id', $data)) {
			$sql = "update {$this->_table} set ";
			
			$first = true;
			foreach($data as $key => $value) {
				if ($key != 'id') {
					$sql .= ($first == false ? ',' : '') . " $key = '{$value}'";

					$values[] = $value;
					
					$first = false;
				}
			}
			$values[] = $data['id'];
			$sql .= " where id = {$data['id']}";
			$statement = $this->_dbh->prepare($sql);
			return $statement->execute($values);
		}
		else {
			$keys = array_keys($data);
			
			$sql = 'insert into ' . $this->_table . '(';
			$sql .= implode(',', $keys);
			$sql .= ')';
			$sql .= ' values (';
			
			$dataValues = array_values($data);
			$first = true;
			foreach($dataValues as $value) {
				$sql .= ($first == false ? ',?' : '?');
				
				$values[] = $value;
				
				$first = false;
			}
			
			$sql .= ')';
			
			$statement = $this->_dbh->prepare($sql);
			if ($statement->execute($values)) {
				return $this->_dbh->lastInsertId();
			}
		}
		
		return false;
	}

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
	{
		$statement = $this->_dbh->prepare("delete from {$this->_table} where id = {$id}");
		return $statement->execute(array($id));
	}

    /**
     * @param $query
     * @return array
     */
    public function query($query)
    {
        $data = $this->_dbh->prepare($query);
        $data->execute();
        return $data->fetchAll();
    }

    public function queryDelete($query)
    {
        $data = $this->_dbh->prepare($query);
        return $data->execute();
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        $data = $this->_dbh->prepare("SELECT * FROM {$this->_table}");
        $data->execute();
        return $data->fetchAll();
    }

}
