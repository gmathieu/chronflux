<?php
abstract class AbstractDatabaseTestCase extends Zend_Test_PHPUnit_DatabaseTestCase
{
    protected $_fixtureDataSet;
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
        $ds  = $this->createFlatXmlDataSet(TEST_ROOT . '/fixtures/' . $this->_fixtureDataSet);
        $rds = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($ds);
        $rds->addFullReplacement('##NULL##', null);

        return $rds;
    }
}