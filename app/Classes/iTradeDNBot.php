<?php


namespace App\Classes;


interface iTradeDNBot
{

    public function getBasket();
    public function getCategories();
    public function getProductByCategory($categoryId);

}
