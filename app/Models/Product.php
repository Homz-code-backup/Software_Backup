<?php

class Product extends Model
{
    protected $table = 'product_catalog';

    public $id;
    public $room_id;
    public $name;
    public $description;
    public $finish;
    public $rate;
    public $type;
    public $discount;
    public $discounted_rate;
    public $city_id;
    public $group_no;
    public $status;
    public $category;
    public $created_at;
    public $updated_at;
}
