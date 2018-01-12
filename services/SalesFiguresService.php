<?php

namespace Craft;

class SalesFiguresService extends BaseApplicationComponent
{

    public function getSales($options)
    {

    	// REQUIRES startDate AND endDate, FORMATTED AS YYYY-MM-DD
        $startDate = $options['startDate'];
        $endDate = $options['endDate'];
        $productIds = $options['productIds'];

        // START BY GETTING THE PRODUCTS LINKED TO THE CLASS
        $variantIds = craft()->salesFigures->getVariants($productIds);

        // GET THE ORDERS FOR THE PERIOD IN QUESTION
        $orders = craft()->salesFigures->getOrdersWithinRange($startDate, $endDate, $variantIds);

        $numberOfSales = 0;
        $valueOfSales = 0;

        // LOOP THROUGH EACH LINEITEM IN EACH ORDER
        foreach ($orders as $order) {
        foreach ($order->getLineItems() as $lineItem) {
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
        $returnObject->variantIds = $variantIds;

        return $returnObject;
                
    }

    public function getVariants($productIds) {

        // GET THE PRODUCTS
        $criteria = craft()->elements->getCriteria('Commerce_Product');
        $criteria->id = $productIds;
        $criteria->limit = NULL;
        $products = $criteria->find();

        $variantIds = [];
        foreach ($products as $product) {
            array_push($variantIds, $product->defaultVariant->id);
        }

        return $variantIds;  

    }

    public function getOrdersWithinRange($startDate, $endDate, $variantIds) {

        $criteria = craft()->elements->getCriteria('Commerce_Order');
        $criteria->dateOrdered = array('and', '>= ' . $startDate, '<= ' . $endDate);
        $criteria->hasPurchasables = $variantIds;
        $criteria->limit = NULL;
        $orders =  $criteria->find();
        return $orders;  

    }

}