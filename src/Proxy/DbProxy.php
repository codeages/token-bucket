<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16-8-3
 * Time: 下午3:43
 */
namespace Codeages\TokenBucket\Proxy;

use Doctrine\DBAL\DriverManager;
use Codeages\TokenBucket\Kit\FileConfig;

class DbProxy extends Proxy
{
    private $config;
    private $table;

    public function __construct($options = array())
    {
        $default = FileConfig::config(FileConfig::Db, array());
        $this->config = array_merge($default, $options);
    }

    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function db()
    {
        if (!$this->storage) {
            $this->storage = DriverManager::getConnection(array(
                'dbname' => $this->config['name'],
                'user' => $this->config['user'],
                'password' => $this->config['password'],
                'host' => $this->config['host'],
                'port' => $this->config['port'],
                'driver' => $this->config['driver'],
                'charset' => $this->config['charset'],
            ));
        }
        return $this->storage;
    }

    public function get($key)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tokenKey = ?";
        return $this->db()->fetchAssoc($sql, array($key)) ?: null;
    }

    public function update($key, $bucket)
    {
        $bucket['tokenKey'] = $key;
        $keys = array_keys($bucket);
        $states = array_fill(0, count($keys), "?");
        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(', ', $keys) . ') ';
        $sqlInserts = array();
        $sqlUpdates = array();
        $sqlInserts[] = "(" . implode(",", $states) . ")";
        $sql .= ' VALUES ' . implode(",", $sqlInserts);
        $sql .= ' ON DUPLICATE KEY UPDATE ';
        foreach ($keys as $key) {
            $sqlUpdates[] = "{$key} = VALUES({$key})";
        }
        $sql .= implode(",", $sqlUpdates);
        $this->db()->executeQuery($sql, array_values($bucket));
    }
}