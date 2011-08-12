<?php
abstract class AbstractDatabaseTestCase extends Zend_Test_PHPUnit_DatabaseTestCase
{
    // by default load entire DB
    protected $_fixtureDataset = 'projects,users,tasks,user_tasks,user_projects,jobs';

    private $_connectionMock;
 
    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        if ($this->_connectionMock == null) {
            $connection            = Zend_Registry::get('db');
            $connectionConfig      = $connection->getConfig();
            $this->_connectionMock = $this->createZendDbConnection(
                $connection,
                $connectionConfig['dbname']
            );

            Zend_Db_Table_Abstract::setDefaultAdapter($connection);
        }
        return $this->_connectionMock;
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        $explodedDataSet = explode(',', $this->_fixtureDataset);

        if (count($explodedDataSet) == 0) {
            $ds = $this->createFlatXmlDataSet($this->_getDatasetPath($this->_fixtureDataset));
        } else {
            $ds = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());
            foreach ($explodedDataSet as $dataSet) {
                $flatDs = $this->createFlatXmlDataSet($this->_getDatasetPath($dataSet));
                $ds->addDataSet($flatDs);
            }
        }

        return $ds;
    }

    private function _getDatasetPath($name)
    {
        $path = TEST_ROOT . '/fixtures/' . $name . '.xml';
        if (file_exists($path)) {
            return $path;
        } else {
            throw new Exception("{$path} not found");
        }
    }
}