<?php
class Employee extends Model
{
    protected $table = 'employee';
    protected $primaryKey = 'employee_id';

    // Optional: Add custom logic
    public function getShortName()
    {
        $parts = explode(' ', $this->full_name);
        return $parts[0];
    }
}
 