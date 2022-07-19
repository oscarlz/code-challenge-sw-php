<?php

namespace App\Controllers;

use App\Exceptions\BasicProductInfoMissingException;
use App\Exceptions\ServerErrorException;
use App\Models\ProductsRepository;

class ProductController 
{
    protected $productsRepository;

    public function __construct()
    {
        $this->productsRepository = new ProductsRepository();
    }

    /**
    * Action to handle the product list. It just fetches all the products with its attributes.
    *
    * @return json
    */
    public function listAction()
    {
        return json_encode($this->productsRepository->getAllProducts());
    }

    /**
     * Action to delete products specified in the array of product ids.
     * 
     * @param array $productIds
     * @return json
     */
    public function deleteProductsAction(array $productIds)
    {
        $deletedProducts = $this->productsRepository->deleteProducts($productIds);
        
        $response = json_encode(($deletedProducts === null ? ['deleted_rows' => 0] : ['deleted_rows' => $deletedProducts]));

        return $response;
    }

    /**
     * Checks if a SKU already exists.
     * 
     * @param array $params
     * @return json
     */
   public function skuExistsAction(array $params)
   {
        $jsonResponse = ($this->productsRepository->skuExists($params['sku']) === true ?  ['exists' => true] : ['exists' => false]);

        return json_encode($jsonResponse);
    }

    /**
     * Adds a product.
     * 
     * @param Array $product
     * @return json
     */
    public function createAction(array $product)
    {
        try{
            $productInfoIsCompleted = $this->productsRepository->isProductInfoCompleted($product);

            if($productInfoIsCompleted === true){
                $newProd = $this->productsRepository->createProduct($product);
        
                $jsonResponse = ($newProd->saveProduct() === true ? ['product_created' => true] : ['product_created' => false]);
        
                return json_encode($jsonResponse);
                
            }else{

                // throw exception for now. Need to implement a error reporting/logging system
                throw new BasicProductInfoMissingException();
            }
            
        }catch(\Throwable $th){
           
            // todo: need to log this error somewhere
            throw new ServerErrorException();
        }
    }
}