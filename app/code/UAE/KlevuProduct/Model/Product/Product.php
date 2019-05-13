<?php

namespace UAE\KlevuProduct\Model\Product;
use Klevu\Search\Model\Product\Product as Klevuproduct;

class Product extends Klevuproduct
{/**
 * Get the list of prices based on customer group
 *
 * @param object $item OR $parent
 *
 * @return array
 */
    protected function getGroupPrices($proData) {
      $writer = new \Zend\Log\Writer\Stream('/var/log/getGroupPricesUAE.log');
      $logger = new \Zend\Log\Logger();
      $logger->addWriter($writer);

      $customer = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Customer\Model\ResourceModel\Group\Collection');
      $priceGroupData = array();

      $logger->info( ' ************************************************************************ ' );

      foreach ($customer as $type) {

        $logger->info( '********************* Product ID: '.$proData->getId() .' ********************* ');
        $logger->info( 'Product SKU: '.$proData->getSku() );
        $logger->info( 'CustomGroupID: '.$type->getCustomerGroupId() );
        $logger->info( 'ProductPriceBefore: '.$proData->getFinalprice() );

        $product = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Catalog\Model\Product')->setCustomerGroupId( $type->getCustomerGroupId())->load( $proData->getId() );

        $logger->info( 'ProductFinalPriceAfter: '.$product->getFinalprice() );

        $final_price = $product->getFinalprice();
        $processed_final_price = $this->_priceHelper->processPrice($final_price, 'final_price', $product, $this->_storeModelStoreManagerInterface->getStore());

        $logger->info( 'ProductPriceProcessAfter: '.$processed_final_price );

         if ($processed_final_price) {
             $result['label'] = $type->getCustomerGroupCode();
             $result['values'] = $processed_final_price;
             $priceGroupData[$product->getCustomerGroupId()] = $result;
         }
         $logger->info( ' ****************************************** ' );
     }
     return $priceGroupData;
   }

}
