<?php

namespace App\Models;

use App\Inc\MysqlConnector;

class ProductsRepository extends MysqlConnector 
{
    
    public function __construct(){
        parent::__construct();
    }

    /**
     * Deletes products specified in the $productsIds param.
     * 
     * @param array $productIds
     * @return mixed It can return int (number of deleted rows) or false if nothing happened. Number of deleted rows is not equal to number of products deleted.
     */
    public function deleteProducts(array $productsIds)
    {
        $query = '';
        $idsCount = count($productsIds);

        if($idsCount > 0){
            try{
                $bindParamsInPlaceholder = implode(',', array_fill(0, $idsCount, '?'));

                $query = "DELETE p.*, pa.* FROM products p INNER JOIN  product_attributes pa ON p.id = pa.product_id WHERE p.id IN ($bindParamsInPlaceholder)";
    
                $this->run($query, $productsIds);
                
                return $this->affectedRows();

            }catch(\Throwable $th){
                
                // todo: need to log this error somewhere
                return false;
            }
        }

        return false;
    }

    /**
     * Creates an instance of the Product based on its type.
     * 
     * @param array @productInfo
     * @return mixed It can return false or a Product instance. It can be a Dvd, Book or Furniture instance.
     */
    public function createProduct(array $productInfo)
    {
        try{
            $className = "\\App\Models\\" . $productInfo['type'];
            $product = new $className($productInfo);

        }catch(\Throwable $e){
            
            // todo: need to log this error somewhere
             return false;
        }

        return $product;
    }

    /**
     * Gets all the products and their attributes.
     * Maybe later move this to a simpler query and delegate the data manipulation to the controller.
     * 
     * @return array
     */
    public function getAllProducts(): array
    {
        $queryStmt = $this->run("SELECT product_id as id, sku, name, price, product_type_code as type_code, MAX(size) as 'size', MAX(weight) as 'weight', 
                                                MAX(height) as 'height', MAX(width) as 'width', MAX(length) as 'length'
                                        FROM (
                                        SELECT pa.product_id, p.sku, p.name, p.price, pa.product_type_code,
                                        (CASE WHEN pa.name = 'size' THEN pa.value END) AS size,
                                        (CASE WHEN pa.name = 'height' THEN pa.value END) AS height,
                                        (CASE WHEN pa.name = 'width' THEN pa.value END) AS width,
                                        (CASE WHEN pa.name = 'weight' THEN pa.value END) AS weight,
                                        (CASE WHEN pa.name = 'length' THEN pa.value END) AS length
                                        FROM product_attributes pa
                                            JOIN products p ON pa.product_id = p.id
                                            ) as subquery
                                        GROUP BY product_id
                                        ORDER BY product_id DESC");
        
        return $queryStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }    

    /**
     * Check if $sku already exists in the table. Returns true when found.
     * 
     * @param string $sku
     * @return boolean
     */
    public function skuExists(string $sku): bool
    {
        $queryStmt = $this->run('SELECT id FROM products WHERE sku=?', [$sku]);
        $queryStmt->store_result();

        return ($queryStmt->num_rows > 0) ? true : false;
    }

    /**
     * Checks if product information is completed, meaning that no data is missing and the product can be created.
     * 
     * @param $productInfo
     * @return bool
     */
    public function isProductInfoCompleted(array $productInfo)
    {

        // Check base parameters first, then product-type specific parameters
        if(!isset($productInfo["sku"]) || !isset($productInfo["name"]) || !isset($productInfo["price"]) || !isset($productInfo["type"])){
            return false;
        }
        
        // var_dump($productInfo); die;
        /**
         * To check type-specific parameters we just try to instantiate the object.
         * The instantion will throw an exception if the parameters needed for this type of product are not provided.
         */
        try{
            $className = '\\App\Models\\' . ucfirst(strtolower($productInfo['type']));
            // echo $className; die;
            $product = new $className($productInfo);

        }catch(\Throwable $e){
            
            // todo: need to log this error somewhere
             return false;
        }

        return true;
    }
}