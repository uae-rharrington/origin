<?php

namespace Klevu\Search\Model\Product;
use Magento\Framework\DataObject;



class Product extends DataObject implements ProductInterface
{/**
 * Get the list of prices based on customer group
 *
 * @param object $item OR $parent
 *
 * @return array
 */
    protected function getGroupPrices($proData)
    {
        $customer = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Customer\Model\ResourceModel\Group\Collection');
        $priceGroupData = array();
        foreach($customer as $type)
        {
            $product = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Catalog\Model\Product')->setCustomerGroupId($type->getCustomerGroupId())->load($proData->getId());
            $final_price = $product->getFinalprice();
            $processed_final_price = $this->_priceHelper->processPrice($final_price,'final_price',$product,$this->_storeModelStoreManagerInterface->getStore());
            if($processed_final_price){
                  $result['label'] = $type->getCustomerGroupCode();
                  $result['values'] = $processed_final_price;
                  $priceGroupData[$product->getCustomerGroupId()]= $result;
              }
          }
          return $priceGroupData;
    }

}
