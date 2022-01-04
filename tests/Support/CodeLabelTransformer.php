<?php

namespace Weble\DataSyncLaravel\Tests\Support;

use Weble\DataSync\Contracts\ItemInterface;
use Weble\DataSync\Contracts\TransformerInterface;
use Weble\DataSync\Item;

class CodeLabelTransformer implements TransformerInterface
{
    public function transform(ItemInterface $item): Item
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
