<?php

namespace App\Models;

use Exception;

class Book extends AbstractProduct 
{
    protected const TYPE_CODE = 'BOOK';
    protected $weight = '';

    public function __construct(array $productInfo)
    {
        if(!isset($productInfo['weight'])){
            throw new Exception("Need to provide the weight of the Book.");
        }

        parent::__construct($productInfo);
        $this->weight = (float) $productInfo['weight'];
    }
    
    public function saveProduct()
    {
        try{
            $this->connection->begin_transaction();

            $this->run('INSERT INTO products (sku, name, price) VALUES (?, ?, ?)', [$this->sku, $this->name, $this->price]);
            $productId = $this->lastInsertID();
            $this->saveProductAttributes($productId);
            
            $this->connection->commit();

            return true;

        }catch (\Throwable $th){
            // todo: need to log this error somewhere
            $this->connection->rollback();

            return false;
        }
    }

    protected function saveProductAttributes($productId)
    {
        $this->run('INSERT INTO product_attributes (product_id, product_type_code, name, value) 
                            VALUES (?, ?, ?, ?)', [$productId, $this::TYPE_CODE, 'weight', $this->weight]);
    }
}