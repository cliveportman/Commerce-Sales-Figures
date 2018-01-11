<?php

namespace Craft;

class SalesFiguresVariable
{

    public function getSales($options = array()) {

    	// REQUIRED OPTIONS ARE startDate AND endDate, FORMATTED AS YYYY-MM-DD
        $sales = craft()->salesFigures->getSales($options);
        return $sales;

    }

}