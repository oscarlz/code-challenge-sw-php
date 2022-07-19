<?php

namespace App\Models;

use Exception;

class Furniture extends AbstractProduct 
{
    const TYPE_CODE = 'FURNITURE';
    protected $height = '';
    protected $width = '';
    protected $length = '';

    public function __construct(array $productInfo)
    {
        if(!isset($productInfo['height']) || !isset($productInfo['width']) || !isset($productInfo['length'])){
            throw new Exception("Need to provide the height, width and length of the Furniture.");
        }
        
        parent::__construct($productInfo);
        $this->height = (float) $productInfo['height'];
        $this->width = (float) $productInfo['width'];
        $this->length = (float) $productInfo['length'];
    }
    
    public function saveProduct()
    {
        try{
            $this->run('INSERT INTO products (sku, name, price) VALUES (?, ?, ?)', [$this->sku, $this->name, $this->price]);
            
            $productId = $this->lastInsertID();
            $this->saveProductAttributes($productId);

            if($this->affectedRows() == 3){
                return true;
            }
        }catch (\Throwable $th){
            
            // todo: need to log this error somewhere
            return false;
        }
    }

    protected function saveProductAttributes($productId)
    {
        $query = "INSERT INTO product_attributes (product_id, product_type_code, name, value) VALUES (?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)";

        $this->run($query, [$productId, $this::TYPE_CODE, 'height', $this->height,
                                        $productId, $this::TYPE_CODE, 'width', $this->width,
                                        $productId, $this::TYPE_CODE, 'length', $this->length,]);
    }
}