<?php

namespace Wizzy\Search\Services\Store;

class StoreAutocompleteConfig
{
    private $configManager;

    const WIZZY_AUTOCOMPLETE_MENU_CONFIGURATION = "wizzy_autocomplete_configuration";

   // General section configuration
    const WIZZY_AUTTOCOMPLETE_MENU_ATTRIBUTES =
       self::WIZZY_AUTOCOMPLETE_MENU_CONFIGURATION . "/autocomplete_attributes_configuration";
    const AUTOCOMPLETE_ENABLED_ATTRIBUTES = self::WIZZY_AUTTOCOMPLETE_MENU_ATTRIBUTES . "/autocomplete_attributes";

    const WIZZY_AUTTOCOMPLETE_MENU = self::WIZZY_AUTOCOMPLETE_MENU_CONFIGURATION . "/autocomplete_menu";
    const WIZZY_AUTOCOMPLETE_MENU_SUGGESTIONS_COUNT = self::WIZZY_AUTTOCOMPLETE_MENU . "/suggestions_count";
    const WIZZY_AUTOCOMPLETE_MENU_CATEGOIRES_TITLE = self::WIZZY_AUTTOCOMPLETE_MENU . "/categories_title";
    const WIZZY_AUTOCOMPLETE_MENU_OTHERS_TITLE = self::WIZZY_AUTTOCOMPLETE_MENU . "/others_title";
    const WIZZY_AUTOCOMPLETE_MENU_ALIGNMENT = self::WIZZY_AUTTOCOMPLETE_MENU . "/alignment";
    const WIZZY_AUTOCOMPLETE_NO_RESULTS_BEHAVIOUR = self::WIZZY_AUTTOCOMPLETE_MENU . "/no_results_behaviour";
    const WIZZY_AUTOCOMPLETE_NO_RESULTS_TEXT = self::WIZZY_AUTTOCOMPLETE_MENU . "/no_results_text";

    const WIZZY_AUTTOCOMPLETE_TOP_PRODUCTS = self::WIZZY_AUTOCOMPLETE_MENU_CONFIGURATION . "/autocomplete_top_products";
    const WIZZY_AUTTOCOMPLETE_SHOW_TOP_PRODUCTS = self::WIZZY_AUTTOCOMPLETE_TOP_PRODUCTS . "/show_products_suggestions";
    const WIZZY_AUTTOCOMPLETE_TOP_PRODUCTS_TITLE = self::WIZZY_AUTTOCOMPLETE_TOP_PRODUCTS . "/top_products_title";
    const WIZZY_AUTTOCOMPLETE_TOP_PRODUCTS_COUNT = self::WIZZY_AUTTOCOMPLETE_TOP_PRODUCTS . "/top_products_count";

    const WIZZY_AUTTOCOMPLETE_PAGES = self::WIZZY_AUTOCOMPLETE_MENU_CONFIGURATION . "/autocomplete_pages";
    const WIZZY_AUTTOCOMPLETE_PAGES_TITLE = self::WIZZY_AUTTOCOMPLETE_PAGES . "/pages_title";
    const WIZZY_AUTTOCOMPLETE_EXCLUDE_PAGES = self::WIZZY_AUTTOCOMPLETE_PAGES . "/exclude_pages";

    private $storeId;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function setStore(string $storeId)
    {
        $this->storeId = $storeId;
    }

    public function getSuggestionsCount()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_AUTOCOMPLETE_MENU_SUGGESTIONS_COUNT, $this->storeId);
    }

    public function getCategoriesTitle()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_AUTOCOMPLETE_MENU_CATEGOIRES_TITLE, $this->storeId);
    }

    public function getOthersTitle()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_AUTOCOMPLETE_MENU_OTHERS_TITLE, $this->storeId);
    }

    public function getMenuAlignment()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_AUTOCOMPLETE_MENU_ALIGNMENT, $this->storeId);
    }

    public function getNoResultsBehaviour()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_AUTOCOMPLETE_NO_RESULTS_BEHAVIOUR, $this->storeId);
    }

    public function getNoResultsText()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_AUTOCOMPLETE_NO_RESULTS_TEXT, $this->storeId);
    }

    public function hasToShowTopProducts()
    {
        return ($this->configManager->getStoreConfig(self::WIZZY_AUTTOCOMPLETE_SHOW_TOP_PRODUCTS, $this->storeId) == 1);
    }

    public function getTopProductsCount()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_AUTTOCOMPLETE_TOP_PRODUCTS_COUNT, $this->storeId);
    }

    public function getTopProductsTitle()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_AUTTOCOMPLETE_TOP_PRODUCTS_TITLE, $this->storeId);
    }

    public function getPagesTitle()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_AUTTOCOMPLETE_PAGES_TITLE, $this->storeId);
    }

    public function getExcludedPages()
    {
        $pagesToExclude = $this->configManager->getStoreConfig(self::WIZZY_AUTTOCOMPLETE_EXCLUDE_PAGES, $this->storeId);
        if (!$pagesToExclude) {
            $pagesToExclude = [];
        } else {
            $pagesToExclude = json_decode($pagesToExclude, true);
            $pages = [];
            foreach ($pagesToExclude as $key => $pageToExclude) {
                $pages[] = $pageToExclude['page'];
            }
            $pagesToExclude = $pages;
        }
        return $pagesToExclude;
    }

    public function getAttributes()
    {
        return $this->configManager->getStoreConfig(self::AUTOCOMPLETE_ENABLED_ATTRIBUTES, $this->storeId);
    }
}
