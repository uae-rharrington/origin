<?php

namespace Swissup\SoldTogether\Block;

class Customer extends Order
{
    const SOLDTOGETHER_ENTITY = 'customer';

    protected $_tableName = 'swissup_soldtogether_customer';
}
