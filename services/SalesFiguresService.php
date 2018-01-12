<?php

namespace Craft;

class SalesFiguresService extends BaseApplicationComponent
{

    public function getSales($options)
    {

    	// REQUIRES startDate AND endDate, FORMATTED AS YYYY-MM-DD
        $startDate = $options['startDate'];
        $endDate = $options['endDate'];
        $classId = $options['classId'];

        // START BY GETTING THE PRODUCTS LINKED TO THE CLASS
        $products = craft()->salesFigures->getProductsRelatedToClass($startDate, $endDate, $classId);

        // USE THESE PRODUCTS TO CREATE A LIST OF VARIANT IDS
        $variantIds = [];
        foreach ($products as $product) {
            echo $product->title;
            array_push($variantIds, $product->defaultVariant->id);
        }

        // GET THE ORDERS FOR THE PERIOD IN QUESTION
        $orders = craft()->salesFigures->getOrdersWithinRange($startDate, $endDate);

        $numberOfSales = 0;
        $valueOfSales = 0;

        // LOOP THROUGH EACH LINEITEM IN EACH ORDER
        foreach ($orders as $order) {
        foreach ($order->getLineItems() as $lineItem) {
            echo $lineItem->purchasableId . "/";
            if (in_array($lineItem->purchasableId, $variantIds)) {
                $numberOfSales = $numberOfSales + $lineItem->qty;
                $valueOfSales = $valueOfSales + $lineItem->total;
            }
        }
        }

        // PREPARE THE RETURN OBJECT
        $returnObject = new \stdClass;
        $returnObject->totalSalesNumber = $numberOfSales;
        $returnObject->totalSalesValue = $valueOfSales;
        $returnObject->classId = $classId;
        $returnObject->variantIds = $variantIds;

        return $returnObject;
                
    }

    public function getProductsRelatedToClass($startDate, $endDate, $classId) {

        // START BY GETTING THE ENTRY FROM ITS ID
        $class = craft()->entries->getEntryById($classId);
        echo $class->title;
        // THEN GET THE PRODUCTS
        $criteria = craft()->elements->getCriteria('Commerce_Product');
        $criteria->date = array('and', '>= ' . $startDate, '<= ' . $endDate);
        $criteria->relatedTo(array(
            'targetElement' => $class,
            'field' => 'class'
        ));
        $criteria->limit = NULL;
        $products = $criteria->find();

        return $products;  

    }

    public function getOrdersWithinRange($startDate, $endDate) {

        $criteria = craft()->elements->getCriteria('Commerce_Order');
        $criteria->dateOrdered = array('and', '>= ' . $startDate, '<= ' . $endDate);
        $orders =  $criteria->find();
        return $orders;  

    }

}