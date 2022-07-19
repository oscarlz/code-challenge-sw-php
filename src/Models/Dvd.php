<?php

namespace App\Models;

use Exception;

class Dvd extends AbstractProduct 
{
    const TYPE_CODE = 'DVD';
    protected $size = 0;

    public function __construct(array $product)
    {
        if(!isset($product["size"])){
            throw new Exception("Need to provide the size of the DVD.");
        }
        parent::__construct($product);
        $this->size = (int) $product["size"];
    }
    
    public function saveProduct()
    {
        try{
            $this->run('INSERT INTO products (sku, name, price) VALUES (?, ?, ?)', [$this->sku, $this->name, $this->price]);
            
            $productId = $this->lastInsertID();
            $this->saveProductAttributes($productId);

            if($this->affectedRows() == 1){
                return true;
            }

        }catch (\Throwable $th){

            // todo: need to log this error somewhere
            return false;
        }
    }

    protected function saveProductAttributes($productId)
    {
        $this->run('INSERT INTO product_attributes (product_id, product_type_code, name, value) 
                            VALUES (?, ?, ?, ?)', [$productId, $this::TYPE_CODE, 'size', $this->size]);
    }
}