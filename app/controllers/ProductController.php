<?php

class ProductController extends Controller {
    public function index() {
        $product = $this->model('Product');
        $products = $product->getAllProducts();
        $this->view('product/index', ['products' => $products], 'Product List');
    }
}
