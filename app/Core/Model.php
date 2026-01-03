<?php

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = [];
    protected static $db;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Get the database connection.
     */
    protected static function getDb() 
    {
        if (!self::$db) {
            self::$db = Database::getConnection();
        }
        return self::$db;
    }

    /**
     * Fill model attributes.
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        return $this;
    }

    /**
     * Magic getter to access attributes like properties.
     */
    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Magic setter to set attributes.
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Find a record by ID.
     * @return static|null
     */
    public static function find($id)
    {
        $instance = new static();
        $stmt = self::getDb()->prepare("SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ? LIMIT 1");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $instance->fill($result);
        }

        return null; // Not found
    }

    /**
     * Get all records.
     * @return static[]
     */
    public static function all()
    {
        $instance = new static();
        $stmt = self::getDb()->query("SELECT * FROM {$instance->table}");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $instance->hydrateAll($results);
    }

    /**
     * Find records matching a condition.
     * Usage: User::where('email', 'foo@bar.com');
     */
    public static function where($column, $value)
    {
        $instance = new static();
        $stmt = self::getDb()->prepare("SELECT * FROM {$instance->table} WHERE {$column} = ?");
        $stmt->execute([$value]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $instance->hydrateAll($results);
    }

    protected function hydrateAll($results)
    {
        $models = [];
        foreach ($results as $row) {
            $models[] = (new static())->fill($row);
        }
        return $models;
    }

    /**
     * Save the current model state (Insert or Update).
     */
    public function save()
    {
        $id = $this->attributes[$this->primaryKey] ?? null;

        if ($id) {
            return $this->update($id);
        } else {
            return $this->insert();
        }
    }

    protected function update($id)
    {
        $fields = [];
        $values = [];

        foreach ($this->attributes as $key => $value) {
            if ($key === $this->primaryKey) continue;
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = ?";
        $stmt = self::getDb()->prepare($sql);
        return $stmt->execute($values);
    }

    protected function insert()
    {
        $keys = array_keys($this->attributes);
        $placeholders = array_fill(0, count($keys), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = self::getDb()->prepare($sql);
        
        $success = $stmt->execute(array_values($this->attributes));
        
        if ($success) {
            $this->attributes[$this->primaryKey] = self::getDb()->lastInsertId();
        }
        
        return $success;
    }
}
