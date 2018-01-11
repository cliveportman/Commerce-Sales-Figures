<?php

namespace Craft;

class SalesFiguresService extends BaseApplicationComponent
{

    public function getSales($options)
    {

    	// REQUIRES startDate AND endDate, FORMATTED AS YYYY-MM-DD
        $startDate = $options['startDate'];
        $endDate = $options['endDate'];

        // $variants = craft()->salesFigures->getVariants();



        /*
        RETURNS
        an array of objects
        each object has a purchasableId as it's index
        and contains the total number of sales and total sales value
        of the purchasable
        */

        // START BY FETCHING THE ORDERS WITHIN THE DATE RANGE
        $orders = craft()->salesFigures->getOrdersWithinRange($startDate, $endDate);

        // GET ALL PURCHASED VARIANTS FROM THE ORDERS
        $purchasedVariants = craft()->salesFigures->getPurchasedVariants($orders);

        // SET THE RETURN ARRAY
        $return = [];


        // LOOP THROUGH THE VARIANTS

        // IF THEY AREN'T IN THE RETURN ARRAY, ADD THEM
        // IF THEY ARE, UPDATE THE RETURN ARRAY WITH THE ADDITIONAL SALE NUMBERS AND VALUES

        return $purchasedVariants;
                
    }

    public function getOrdersWithinRange($startDate, $endDate) {

        $criteria = craft()->elements->getCriteria('Commerce_Order');
        $criteria->dateOrdered = array('and', '>= ' . $startDate, '<= ' . $endDate);
        $orders =  $criteria->find();
        return $orders;  

    }

    public function getPurchasedVariants($orders) {

        // CREATE AN ARRAY TO HOLD ALL OF THE LINEITEMS' PURCHASABLES
        $purchasedVariants = [];

        // LOOP THROUGH EACH LINEITEM IN EACH ORDER
        foreach ($orders as $order) {
        	foreach ($order->getLineItems() as $lineItem) {
	            if ($lineItem->purchasableId) {
	            	array_push($purchasedVariants, $lineItem->purchasable);
	            }
	        }
        }

        return $purchasedVariants;

    }

    public function getAllVariants() {

    	/********************
    	NOT CURRENTLY USING
    	WE STARTED THIS PLUGIN BY LOOPING THROUGH THE VARIANTS TO CREATE A LIST,
    	THEN CROSS-CHECKING THEM WITH ORDERS FROM THE SELECTED PERIOD.
    	BUT THE NUMBER OF VARIANTS MAKES THIS TOO INTENSIVE.
    	INSTEAD WE'RE TRYING LOOPING THROUGH THE ORDERS WITHIN THE RANGE.
    	********************/

        // START BY CREATING AN ARRAY OF ALL THE COMMERCE VARIANTS
        $criteria = craft()->elements->getCriteria('Commerce_Variant');
        $criteria->limit = NULL;
        $variants =  $criteria->find();

        // AND ANOTHER ARRAY OF VARIANTS WE WANT TO RETURN
        $simplifiedVariants = [];

        // LOOP THROUGH THE COMMERCE VARIANTS
        foreach ($variants as $variant) {
            
            // AND CREATE A SIMPLIFIED VARIANT OBJECT BEFORE ADDING IT TO THE RETURN ARRAY
            $simplifiedVariant = new \stdClass;
            $simplifiedVariant->title = $variant->id;
            $simplifiedVariant->id = $variant->title;
            $simplifiedVariant->salesNumber = 0;
            $simplifiedVariant->salesValue = 0;
            array_push($simplifiedVariants, $simplifiedVariant);

        }

        return $simplifiedVariants;

    }

}