<?php
namespace Wizzy\Search\Model\Indexer;

use Wizzy\Search\Services\Catalogue\ProductsManager;
use Wizzy\Search\Services\Indexer\IndexerOutput;
use Wizzy\Search\Services\Model\EntitiesSync;
use Wizzy\Search\Services\Queue\Processors\IndexProductsProcessor;
use Wizzy\Search\Services\Queue\QueueManager;
use Magento;
use Wizzy\Search\Services\Store\StoreManager;

class Products implements Magento\Framework\Indexer\ActionInterface, Magento\Framework\Mview\ActionInterface
{

    private $productsManager;
    private $queueManager;
    private $maxProductsInSingleQueue;
    private $entitesSync;
    private $storeManager;
    private $output;

    public function __construct(
        ProductsManager $productsManager,
        QueueManager $queueManager,
        EntitiesSync $entitiesSync,
        StoreManager $storeManager,
        IndexerOutput $output
    ) {
        $this->productsManager = $productsManager;
        $this->queueManager = $queueManager;

      // This needs to be moved into module settings, Max it can be 2000.
        $this->maxProductsInSingleQueue = 2000;

        $this->entitesSync = $entitiesSync;
        $this->storeManager = $storeManager;
        $this->output = $output;
    }

  /*
   * Allows process indexer in the "Update on schedule" mode.
   * Add set of scheduled entities to Wizzy for reindexing.
   */
    public function execute($ids)
    {
        $this->addProductsInQueue($ids, '', true);
    }

  /*
   * Add all data to Wizzy Queue for reindexing
   */
    public function executeFull()
    {
        $products = $this->getAllProductIds();
        $this->addProductsInQueue($products);
    }

    private function getAllProductIds()
    {
        $products = $this->productsManager->getAllProductIds();
        return $products;
    }

  /*
   * Add set of entities to Wizzy Queue for reindexing
   */
    public function executeList(array $ids)
    {
        $this->addProductsInQueue($ids, '', true);
    }

  /*
   * Add specific row to Wizzy Queue for reindexing
   */
    public function executeRow($id)
    {
        $this->addProductsInQueue([$id], $this->storeManager->getCurrentStoreId(), true);
    }

    private function addProductsInQueue(array $productIdsToProcess, $storeId = '', $combinePreviousEntries = false)
    {
        if (count($productIdsToProcess) == 0) {
          // Return as no products to process.
            return;
        }

        $storeIds = $this->storeManager->getToSyncStoreIds($storeId);

        foreach ($storeIds as $storeId) {
            $productIds = $this->getProductIdsToSync($productIdsToProcess, $storeId);
            $productBatchIds = [];
            $addedProducts = 0;
            $batchIndex = 0;
            $this->output->writeDiv();
            $this->output->writeln(__('Adding ' . count($productIds) .' Products for Sync in Store #' . $storeId));
            $combinePreviousEntriesOfStore = $combinePreviousEntries;

            foreach ($productIds as $productId) {
                if ($addedProducts == $this->maxProductsInSingleQueue) {
                    $addedProducts = 0;
                    $batchIndex++;
                }
                if ($addedProducts == 0) {
                    $productBatchIds[$batchIndex] = [];
                }
                $productBatchIds[$batchIndex][] = $productId;
                $addedProducts++;
            }

            if (count($productBatchIds) > 1) {
                $combinePreviousEntriesOfStore = false;
            }

            if ($combinePreviousEntriesOfStore) {
                $jobData = $this->queueManager->getLatestInQueueByClass(IndexProductsProcessor::class, $storeId);
            }

            foreach ($productBatchIds as $productIds) {
                $hasEdited = false;

                if ($combinePreviousEntriesOfStore && $jobData !== null) {
                    $existingProducts = json_decode($jobData['data'], true);
                    $existingProducts = $existingProducts['products'];

                    $existingProducts = $this->mergeProductIds($existingProducts, $productIds);

                    if (count($existingProducts) <= $this->maxProductsInSingleQueue) {
                        $jobData['data'] = json_encode([
                         'products' => $existingProducts,
                        ]);
                        $this->queueManager->edit($jobData);
                        $hasEdited = true;
                        $combinePreviousEntriesOfStore = false;
                    }
                }

                if ($hasEdited === false) {
                    $this->queueManager->enqueue(IndexProductsProcessor::class, $storeId, [
                       'products' => $productIds,
                    ]);
                }

                $this->entitesSync->addEntitiesToSync($productIds, $storeId, EntitiesSync::ENTITY_TYPE_PRODUCT);
            }
        }
    }

    private function mergeProductIds(array $productIdsA, array $productIdsB)
    {
        $mergedIds = [];

        foreach ($productIdsA as $productId) {
            $mergedIds[$productId] = true;
        }

        foreach ($productIdsB as $productId) {
            $mergedIds[$productId] = true;
        }

        $mergedIds = array_keys($mergedIds);
        $mergedIds = array_map(function ($value) {
            return (string) $value;
        }, $mergedIds);
        
        return $mergedIds;
    }

    private function getProductIdsToSync($productIds, $storeId)
    {
        return $this->entitesSync->filterEntitiesYetToSync($productIds, $storeId, EntitiesSync::ENTITY_TYPE_PRODUCT);
    }
}
