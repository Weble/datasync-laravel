<?php

namespace Weble\DataSyncLaravel\Tests\Support;

use Weble\DataSync\Contracts\ItemInterface;
use Weble\DataSync\Contracts\ItemProcessorInterface;
use Weble\DataSync\Item;

class CodeLabelTransformer implements ItemProcessorInterface
{
    public function process(ItemInterface $item): ItemInterface
    {
        if (empty($item->get('country'))) {
            return $item->skip();
        }

        return new Item([
            'code' => $item['country'],
            'name' => $item['name'],
        ]);
    }
}
