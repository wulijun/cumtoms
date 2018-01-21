<?php
namespace app\applibs;

use Yii;
use MongoId;

abstract class MongoBaseModel extends \yii\base\Model
{
    private $_attributes = array(); // attribute name => attribute value
    private $_pk; // old primary key value
    private $_db = null;
    
    const PK_TYPE_MONGOID = 1;
    const PK_TYPE_STRING = 2;
    const PK_TYPE_INT = 3;
    
    public function __construct($config = [])
    {
        $this->_attributes = $this->getAttributeDefaults();
        parent::__construct($config);
    }
    
    public function attributes()
    {
        return array_keys($this->getAttributeDefaults());
    }

    
    /**
     * 返回记录的各个字段的默认值
     */
    abstract protected function getAttributeDefaults();
    
    /**
     * Returns the name of the associated database table.
     * @return array the database name and table name.
     */
    abstract public function tableName();
    
    /**
     * Returns the primary key type of the associated database table.
     * @return int one of self::PK_TYPE_* constant
     */
    abstract public function pkType();
    
    public function checkAndGetMongoId($pks)
    {
        if (! is_array($pks)) {
            if (MongoId::isValid($pks)) {
                return new MongoId($pks);
            }
        } else {
            $tmp = [];
            foreach ($pks as $v) {
                if (MongoId::isValid($v)) {
                    $tmp[] = new MongoId($v);
                }
            }
            if (! empty($tmp)) {
                return $tmp;
            }
        }
        return false;
    }
    
    /**
     * 把参数转换为主键的类型值
     * 
     * @param mixed $pks 需要转换的主键值
     * @return mixed 转换后的主键值
     */
    public function getPkValues($pks)
    {
        $type = $this->pkType();
        if (! is_array($pks)) {
            if ($type == self::PK_TYPE_INT) {
                $pks = (int) $pks;
            } elseif ($type == self::PK_TYPE_STRING) {
                $pks = (string) $pks;
            } else {
                $pks = new MongoId($pks);
            }
        } else {
            foreach ($pks as $k => $v) {
                if ($type == self::PK_TYPE_INT) {
                    $pks[$k] = (int) $v;
                } elseif ($type == self::PK_TYPE_STRING) {
                    $pks[$k] = (string) $v;
                } else {
                    $pks[$k] = new MongoId($v);
                }
            }
        }
        return $pks;
    }
    
    /**
     * Returns the database connection used by active record.
     * You may override this method if you want to use a different database connection.
     * @return \MongoClient the database connection used by active record.
     */
    public function getDbConnection()
    {
        if($this->_db === null) {
            $table = $this->tableName();
            $this->_db = MongoDbConnection::getDb($table['config']);
        }
        return $this->_db;
    }
    
    /**
     * @return \MongoDB
     */
    public function getDB()
    {
        $table = $this->tableName();
        $dbName = MongoDbConnection::getName($table['config']);
        return $this->getDbConnection()->selectDB($dbName);
    }
    
    /**
     * @return \MongoCollection
     */
    protected function getCollection()
    {
        $table = $this->tableName();
        if($this->_db === null) {
            $this->_db = MongoDbConnection::getDb($table['config']);
        }
        return $this->_db->selectCollection(MongoDbConnection::getName($table['config']), $table['table']);
    }
    
    /**
     * PHP getter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @return mixed property value
     * @see getAttribute
     */
    public function __get($name)
    {
        if(array_key_exists($name, $this->_attributes))
            return $this->_attributes[$name];
        else
            return parent::__get($name);
    }
    
    /**
     * PHP setter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @param mixed $value property value
     */
    public function __set($name, $value)
    {
        if($this->setAttribute($name, $value) === false) {
            parent::__set($name, $value);
        }
    }
    
    /**
     * Checks if a property value is null.
     * This method overrides the parent implementation by checking
     * if the named attribute is null or not.
     * @param string $name the property name or the event name
     * @return boolean whether the property value is null
     */
    public function __isset($name)
    {
        if(array_key_exists($name, $this->_attributes)) {
            return isset($this->_attributes[$name]);
        } else {
            return parent::__isset($name);
        }
    }
    
    /**
     * Sets a component property to be null.
     * This method overrides the parent implementation by clearing
     * the specified attribute value.
     * @param string $name the property name or the event name
     */
    public function __unset($name)
    {
        if(array_key_exists($name, $this->_attributes))
            unset($this->_attributes[$name]);
        else
            parent::__unset($name);
    }
    
    /**
     * Returns whether there is an element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param mixed $offset the offset to check on
     * @return boolean
     */
    public function offsetExists($offset)
    {
        if(array_key_exists($offset, $this->_attributes)) {
            return true;
        } else {
            return parent::offsetExists($offset);
        }
    }
    
    /**
     * Sets the named attribute value.
     * You may also use $this->AttributeName to set the attribute value.
     * @param string $name the attribute name
     * @param mixed $value the attribute value.
     * @return boolean whether the attribute exists and the assignment is conducted successfully
     * @see hasAttribute
     */
    public function setAttribute($name, $value)
    {
        if(property_exists($this,$name))
            $this->$name=$value;
        elseif(array_key_exists($name, $this->_attributes))
            $this->_attributes[$name]=$value;
        else
            return false;
        return true;
    }

    /**
     * Finds a single record that has the specified attribute values.
     * @param array $attributes list of attribute values (indexed by attribute names) that the records should match.
     * @return MongoBaseModel the record found. Null if none is found.
     */
    public function findByAttributes($attributes)
    {
        $coll = $this->getCollection();
        $doc = $coll->findOne($attributes);
        if (empty($doc)) {
            return null;
        }
        $o = $this->instantiate();
        $o->_attributes = array_merge($o->_attributes, $doc);
        return $o;
    }
    
    /**
     * Finds a single record that has the specified attribute values.
     * @param array $attributes list of attribute values (indexed by attribute names) that the records should match.
     * @return array the record found. Null if none is found.
     */
    public function findDoc($attributes)
    {
        $coll = $this->getCollection();
        $doc = $coll->findOne($attributes);
        if (empty($doc)) {
            return null;
        }
        return $doc;
    }
    
    /**
     * Finds all records that has the specified attribute values.
     * @param array $attributes list of attribute values (indexed by attribute names) that the records should match.
     * @param array $fields Fields of the results to return. The array is in the format array('fieldname' => true, 'fieldname2' => true). The _id field is always returned.
     * @return \MongoCursor Returns a cursor for the search results.
     */
    public function findAllByAttributes($attributes, $fields=array())
    {
        $coll = $this->getCollection();
        return $coll->find($attributes, $fields);
    }
    
    /**
     * Finds all records that has the specified primary key values.
     * @param array $attributes list of attribute values (indexed by attribute names) that the records should match.
     * @param array $fields Fields of the results to return. The array is in the format array('fieldname' => true, 'fieldname2' => true). The _id field is always returned.
     * @return \MongoCursor Returns a cursor for the search results.
     */
    public function findAllByPk($pk, $fields=array())
    {
        if (! is_array($pk)) {
            $pk = array($pk);
        }
        $pk = $this->getPkValues($pk);
        $criteria = array('_id'=>array('$in'=>$pk));
        return $this->findAllByAttributes($criteria, $fields);
    }
    
    /**
     * Finds a single active record with the specified primary key.
     * 
     * @param mixed $pk primary key value
     * 
     * @return MongoBaseModel the record found. Null if none is found.
     */
    public function findByPk($pk)
    {
        $pk = $this->getPkValues($pk);
        return $this->findByAttributes(array('_id'=>$pk));
    }
    
    public function count($attributes=[], $options=[])
    {
        return $this->getCollection()->count($attributes, $options);
    }
    
    /**
     * 插入文档
     * 
     * @param array $attributes 要插入的文档
     * @param array $options 插入选项
     * @return mixed 插入失败返回false，插入成功返回_id字段，若w选项设置为0则返回true
     */
    public function insert($attributes, $options=null)
    {
        $coll = $this->getCollection();
        $options2 = array('j'=>false, 'w'=>1);
        if ($options) {
            $options2 = array_merge($options2, $options);
        }
        $ret = false;
        try {
            /**
             * 插入成功: Array([ok]=>1 [n]=>0 [lastOp]=>MongoTimestamp Object ([sec] => 1409647316 [inc] => 1) [electionId] => MongoId Object([$id] => 540522ca35f629ee05cc2f21) [err] => [errmsg] =>)
             * 且$attributes含_id字段, 如果主键冲突等错误会抛出异常
             */
            $ret = $coll->insert($attributes, $options2);
        } catch (\Exception $e) {
            $msg = http_build_query($attributes);
            $msg = $e->getMessage()."($msg)";
            Yii::warning($msg);
        }
        if ($ret === true || (is_array($ret) && empty($ret['err']))) {
            if (isset($attributes['_id'])) {
                return $attributes['_id'];
            } else {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 保存文档
     * 
     * 如果文档存在则更新相关字段，不存在则插入
     * 
     * @param array $attributes 要保存的文档
     * @param array $options 保存选项
     * @return mixed 失败返回false，成功返回_id字段，若w选项设置为0则返回true
     */
    public function save($attributes, $options=null)
    {
        $coll = $this->getCollection();
        $options2 = array('j'=>false, 'w'=>1);
        if ($options) {
            $options2 = array_merge($options2, $options);
        }
        $ret = false;
        try {
            /**
             * 插入成功: Array([ok] => 1 [n] => 0 [lastOp] => MongoTimestamp Object ( [sec] => 1409649513 [inc] => 1 ) [electionId] => MongoId Object ( [$id] => 540522ca35f629ee05cc2f21 ) [err] => [errmsg] => )
             * 且$attributes含_id字段, 如果主键冲突等错误会抛出异常
             * 更新成功: Array([ok] => 1 [nModified] => 1 [n] => 1 [lastOp] => MongoTimestamp Object ( [sec] => 1409649621 [inc] => 1 ) [electionId] => MongoId Object ( [$id] => 540522ca35f629ee05cc2f21 ) [err] => [errmsg] => [updatedExisting] => 1 )
             */
            $ret = $coll->save($attributes, $options2);
        } catch (\Exception $e) {
            $msg = http_build_query($attributes);
            $msg = $e->getMessage()."($msg)";
            Yii::warning($msg);
        }
        if ($ret === true || (is_array($ret) && empty($ret['err']))) {
            if (isset($attributes['_id'])) {
                return $attributes['_id'];
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * 批量插入文档
     *
     * @param array $docs 文档数组
     * @param array $options 插入选项
     * @return mixed 失败返回false，成功返回true
     */
    public function batchInsert($attributes, $options=null)
    {
        $coll = $this->getCollection();
        $options2 = array('j'=>false, 'w'=>1, 'continueOnError'=>false);
        if ($options) {
            $options2 = array_merge($options2, $options);
        }
        $ret = false;
        try {
            /**
             * 插入成功: Array([lastOp]=>MongoTimestamp Object ([sec] => 1409650148 [inc] => 2) [connectionId] => 2 [n] => 0 [syncMillis] => 0 [writtenTo] => [err] => [ok] => 1)
             * continueOnError = true 时遇到错误仍然会抛出异常，但是错误之后的正确的insert仍会执行，如果为false，则从遇到错误之后的记录就不管正确与否均抛弃
             */
            $ret = $coll->batchInsert($attributes, $options2);
        } catch (\Exception $e) {
            $msg = http_build_query($attributes);
            $msg = $e->getMessage()."($msg)";
            Yii::warning($msg);
        }
        if ($ret === true || (is_array($ret) && empty($ret['err']))) {
            return true;
        }
        return false;
    }
    
    /**
     * 更新记录，如果记录不存在则插入
     * 
     * @param array $criteria 更新条件
     * @param array $attributes 更新字段
     * @param array $options 更新选项
     * @return mixed 失败返回false，成功返回array('nModified'=>被修改的记录数, 'upserted'=>如果是插入，则为_id，否则为空)
     */
    public function update($criteria, $attributes, $options=null)
    {
        $coll = $this->getCollection();
        $options2 = array('upsert'=>true, 'multiple'=>true, 'j'=>false, 'w'=>1);
        if ($options) {
            $options2 = array_merge($options2, $options);
        }
        $ret = false;
        try {
            /**
             * 更新一条: Array([ok]=>1 [nModified]=>1 [n]=>1 [lastOp]=>MongoTimestamp Object ([sec] => 1409645858 [inc] => 1) [electionId]=>MongoId Object ([$id] => 540522ca35f629ee05cc2f21 ) [err]=> [errmsg]=> [updatedExisting]=>1)
             * 插入一条: Array([ok]=>1 [nModified]=>0 [n]=>1 [lastOp]=>MongoTimestamp Object ([sec] => 1409646235 [inc] => 1) [electionId]=>MongoId Object ([$id] => 540522ca35f629ee05cc2f21 ) [err]=> [errmsg]=> [upserted]=>1-330111445 [updatedExisting]=>)
             * upsert为0且没有更新:Array([ok]=>1 [nModified]=>0 [n]=>0 [lastOp]=>MongoTimestamp Object ([sec] => 1409645858 [inc] => 1) [electionId] => MongoId Object ([$id] => 540522ca35f629ee05cc2f21) [err]=> [errmsg]=> [updatedExisting]=> ) 
             */
            $ret = $coll->update($criteria, $attributes, $options2);
        } catch (\Exception $e) {
            $msg = http_build_query($criteria + $attributes);
            $msg = $e->getMessage()."($msg)";
            Yii::warning($msg);
        }
        if ($ret === true || (is_array($ret) && empty($ret['err']))) {
            $ret = array(
                'nModified'=>isset($ret['nModified']) ? 0 : $ret['nModified'],
                'upserted'=>empty($ret['upserted']) ? null : $ret['upserted']
            );
        }

        return $ret;
    }
    
    /**
     * 修改表中已存在的字段
     *
     * @param array $criteria 更新条件
     * @param array $attributes 更新字段
     * @param array $options 更新选项
     * @return mixed 失败返回false，成功返回被修改的记录数
     */
    public function modifyExistField($criteria, $attributes, $options=null)
    {
        $attributes = array_intersect_key($attributes, $this->_attributes);
        if (empty($attributes)) {
            return 0;
        }
        $ret = $this->update($criteria, array('$set'=>$attributes), array('upsert'=>false));
        if (isset($ret['nModified'])) {
            return $ret['nModified'];
        }
        return false;
    }
    
    /**
     * 删除文档
     *
     * @param array $criteria 删除条件
     * @param array $options 删除选项
     * @return mixed 删除失败返回false，成功返回删除的记录数，若w选项设置为0则返回true
     */
    public function remove($criteria, $options=null)
    {
        $coll = $this->getCollection();
        $options2 = array('j'=>false, 'w'=>1, 'justOne'=>false);
        if ($options) {
            $options2 = array_merge($options2, $options);
        }
        $ret = false;
        try {
            /**
             * 删除成功: Array([ok]=>1 [n]=>2 [lastOp]=>MongoTimestamp Object ([sec] => 1409647225 [inc] => 1) [electionId]=>MongoId Object([$id] => 540522ca35f629ee05cc2f21) [err]=> [errmsg] =>)
             */
            $ret = $coll->remove($criteria, $options2);
        } catch (\Exception $e) {
            $msg = http_build_query($criteria);
            $msg = $e->getMessage()."($msg)";
            Yii::warning($msg);
        }
        if ($ret === true || (is_array($ret) && empty($ret['err']))) {
            if (is_array($ret) && isset($ret['n'])) {
                return $ret['n'];
            } else {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 修改并返回修改的记录
     * 
     * @param array $criteria 查找条件
     * @param array $attributes 更新字段
     * @param array $fields 需要返回的字段
     * @param array $options 修改选项
     * @return mixed 失败返回false，成功返回匹配的记录，若选项upsert为true且new为false，则在发生插入记录时返回NULL
     */
    public function findAndModify($criteria, $attributes=array(), $fields=array(), $options=null)
    {
        $coll = $this->getCollection();
        $options2 = array('remove'=>false, 'upsert'=>true, 'new'=>false);
        if ($options) {
            $options2 = array_merge($options2, $options);
        }
        $ret = false;
        try {
            /**
             * array('$inc'=>array('data'=>1), '$setOnInsert'=>array('data'=>1000))这种写法是不行的，$setOnInsert
             * 里的字段和前面的重复了
             * 如果new为false，upsert为true，插入数据时返回是NULL，因为新纪录之前没有数据
             */
            $ret = $coll->findAndModify($criteria, $attributes, $fields, $options2);
        } catch (\Exception $e) {
            $msg = http_build_query($criteria);
            $msg = $e->getMessage()."($msg)";
            Yii::warning($msg);
        }
        return $ret;
    }
    
    /**
     * 更新计数器
     * 
     * @param array $criteria 查找条件
     * @param array $attributes 更新的计数器，如array('counter_name' => $step_value)
     * @param bool $insertNotExist 记录不存在时是否插入新纪录，默认不插入
     * @return array 返回计数器更新后的值，失败返回false
     */
    public function updateCounter($criteria, $attributes, $insertNotExist = false)
    {
        $options = ['upsert' => (bool) $insertNotExist, 'new' => true];
        $fields = array_fill_keys(array_keys($attributes), true);
        $ret = $this->findAndModify($criteria, array('$inc' => $attributes), $fields, $options);
        return $ret;
    }

    protected function instantiate()
    {
        $class = static::className();
        $model = new $class();
        return $model;
    }
}