<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";

class ProductController extends Controller {
    public function index() {
        $this->view("product/product_page", [
            'title' => 'Product Masterfile - IMS'
        ]);
    }

    public function add() {
        $this->view("product/product_page", [
            'title' => 'Add Product - IMS'
        ]);
    }
}
?>
