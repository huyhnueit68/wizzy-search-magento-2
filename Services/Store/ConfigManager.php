<?php

namespace Wizzy\Search\Services\Store;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigManager {

   const CATALOGUE_CONFIG = "catalogue_config";
   const STORE_SEARCH_CONFIG = "store_config";
   const AUTOCOMPLETE_ATTRIBUTES_CONFIG = "autocomplete_attributes_config";
   const PAGES_EXCLUDE_CONFIG = "pages_exclude_config";

   private $configWriter;
   private $storeManager;
   private $scopeConfig;

   public function __construct(WriterInterface $configWriter, StoreManager $storeManager, ScopeConfigInterface $scopeConfig) {
      $this->configWriter = $configWriter;
      $this->storeManager = $storeManager;
      $this->scopeConfig = $scopeConfig;
   }

   public function save($key, $value, $scope, $scopeId, $isCustomConfig = TRUE) {
      if ($isCustomConfig) {
         $key = $this->getHiddenKey($key);
      }
      $this->configWriter->save($key, $value, $scope, $scopeId);
   }

   public function saveStoreConfig($key, $value, $isCustomConfig = TRUE) {
      $this->save($key, $value, ScopeInterface::SCOPE_STORES, $this->storeManager->getCurrentStoreId(), $isCustomConfig);
   }

   private function get($key, $scope, $scopeId, $isCustom = FALSE) {
      if ($isCustom) {
         $key = $this->getHiddenKey($key);
      }
      return $this->scopeConfig->getValue($key, $scope, $scopeId);
   }

   public function getStoreConfig($key, $storeId) {
      return $this->get($key, ScopeInterface::SCOPE_STORES, $storeId);
   }

   public function getCustomStoreConfig($key, $storeId) {
      return $this->get($key, ScopeInterface::SCOPE_STORES, $storeId, TRUE);
   }

   private function getHiddenKey($key) {
      return 'wizzy_custom_configs/' . $key;
   }

}