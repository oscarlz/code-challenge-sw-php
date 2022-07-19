<?php 

namespace App\Models;

use App\Inc\MysqlConnector;

abstract class AbstractProduct extends MysqlConnector 
{
    protected $name = '';
    protected $sku = '';
    protected $price = 0.0;

    public function __construct(array $productInfo)
    {
        parent::__construct();
        $this->name = (string) $productInfo['name'];
        $this->sku = (string) $productInfo['sku'];
        $this->price = (float) $productInfo['price'];
    }

    /**
     * Saves the product to the database. It also saves product's attributes.
     * Return true if it created something. It first inserts 1 row on the product table and then it calls the saveProductAttributes() to insert the product's attributes
     *
     * @return bool
     */
    abstract protected function saveProduct();

    /**
     * Saves the product's attributes to the database.
     * 
     * @param int $productId
     * @return void
     */
    abstract protected function saveProductAttributes(int $productId);

}