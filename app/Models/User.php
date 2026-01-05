<?php
class User extends Model
{
    protected $table = 'users';

    // Optional: Add custom logic
    public function getActiveStatus()
    {
        return $this->status === 'Active';
    }
}
 