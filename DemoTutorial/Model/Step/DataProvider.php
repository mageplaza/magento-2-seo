<?php
namespace Mageplaza\DemoTutorial\Model\Step;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mageplaza\DemoTutorial\Model\ResourceModel\Step\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    protected $collection;

    public function __construct(
        CollectionFactory $collectionFactory,
                          $name,
                          $primaryFieldName,
                          $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        $items = $this->collection->getItems();
        $result = [];

        foreach ($items as $item) {
            $result[$item->getId()] = $item->getData();
        }

        return $result;
    }
}
