<?php

namespace Wizzy\Search\Services\Store;

class StoreSearchConfig
{
    private $configManager;

    const WIZZY_SEARCH_CONFIGURATION = "wizzy_search_configuration";

    const WIZZY_SEARCH_RESULTS_CONFIGURATION =
       self::WIZZY_SEARCH_CONFIGURATION . "/search_results_general_configuration";
    const WIZZY_DOM_SELECTOR = self::WIZZY_SEARCH_RESULTS_CONFIGURATION . "/dom_selector";
    const WIZZY_SEARCH_ENDPOINT = self::WIZZY_SEARCH_RESULTS_CONFIGURATION . "/search_endpoint";
    const WIZZY_NO_OF_PRODUCTS = self::WIZZY_SEARCH_RESULTS_CONFIGURATION . "/no_of_products";
    const WIZZY_DISPLAY_ADD_TO_CART_BUTTON =
       self::WIZZY_SEARCH_RESULTS_CONFIGURATION . "/display_add_to_cart_button";
    const WIZZY_DISPLAY_ADD_TO_WISHLIST_BUTTON =
       self::WIZZY_SEARCH_RESULTS_CONFIGURATION . "/display_add_to_wishlist_button";

    const WIZZY_SEARCH_FACETS_CONFIGURATION = self::WIZZY_SEARCH_CONFIGURATION . "/search_results_facets_configuration";
    const WIZZY_FACETS = self::WIZZY_SEARCH_FACETS_CONFIGURATION . "/facets_configuration";
    const WIZZY_FACET_CATEGORY_DISPLAY = self::WIZZY_SEARCH_FACETS_CONFIGURATION . "/category_facet_display_method";

    const WIZZY_SEARCH_SORT_CONFIGURATION = self::WIZZY_SEARCH_CONFIGURATION . "/search_results_sorts_configuration";
    const WIZZY_SORT_CONFIGURATION =
       self::WIZZY_SEARCH_SORT_CONFIGURATION . "/sorts_configuration";

    const WIZZY_PAGINATION_CONFIGURATION =
       self::WIZZY_SEARCH_CONFIGURATION . "/search_results_pagination_configuration";
    const WIZZY_PAGINATION_TYPE = self::WIZZY_PAGINATION_CONFIGURATION . "/pagination_type";
    const WIZZY_PAGINATION_MOVE_TO_TOP_WIDGET =
       self::WIZZY_PAGINATION_CONFIGURATION . "/pagination_move_to_top_widget";

    const WIZZY_SEARCH_SWATCHES_CONFIGURATION =
       self::WIZZY_SEARCH_CONFIGURATION . "/search_results_swatches_configuration";
    const WIZZY_SWATCHES_CONFIGURATION = self::WIZZY_SEARCH_SWATCHES_CONFIGURATION . "/swatches_configuration";

    private $storeId;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function setStore(string $storeId)
    {
        $this->storeId = $storeId;
    }

    public function getDOMSelector()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_DOM_SELECTOR, $this->storeId);
    }

    public function getSearchEndpoint()
    {
        $endPoint = $this->configManager->getStoreConfig(self::WIZZY_SEARCH_ENDPOINT, $this->storeId);
        if (!$endPoint) {
           // default endpoint.
            return "/search";
        }
        return $endPoint;
    }

    public function getNoOfProducts()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_NO_OF_PRODUCTS, $this->storeId);
    }

    public function hasToDisplayAddToCartButton()
    {
        return ($this->configManager->getStoreConfig(self::WIZZY_DISPLAY_ADD_TO_CART_BUTTON, $this->storeId) == 1);
    }

    public function hasToDisplayAddToWishlistButton()
    {
        return ($this->configManager->getStoreConfig(self::WIZZY_DISPLAY_ADD_TO_WISHLIST_BUTTON, $this->storeId) == 1);
    }

    public function getFacetsConfiguration()
    {
        $facetsConfig = $this->configManager->getStoreConfig(self::WIZZY_FACETS, $this->storeId);
        if (!$facetsConfig) {
            return [];
        }

        return json_decode($facetsConfig, true);
    }

    public function getCategoryDisplayMethod()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_FACET_CATEGORY_DISPLAY, $this->storeId);
    }

    public function getSortConfiguration()
    {
        $sortConfigs = $this->configManager->getStoreConfig(self::WIZZY_SORT_CONFIGURATION, $this->storeId);
        if (!$sortConfigs) {
            return [];
        }

        return json_decode($sortConfigs, true);
    }

    public function getSwatchesConfiguration()
    {
        $swatchesConfiguration = $this->configManager->getStoreConfig(
            self::WIZZY_SWATCHES_CONFIGURATION,
            $this->storeId
        );
        if (!$swatchesConfiguration) {
            return [];
        }

        return json_decode($swatchesConfiguration, true);
    }

    public function getPaginationType()
    {
        return $this->configManager->getStoreConfig(self::WIZZY_PAGINATION_TYPE, $this->storeId);
    }

    public function hasToAddMoveToTopWidget()
    {
        return ($this->configManager->getStoreConfig(self::WIZZY_PAGINATION_MOVE_TO_TOP_WIDGET, $this->storeId) == 1);
    }
}
