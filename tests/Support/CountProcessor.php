<?php

namespace Weble\DataSyncLaravel\Tests\Support;

use Weble\DataSync\Contracts\ItemInterface;
use Weble\DataSync\Contracts\ProcessorInterface;

class CountProcessor implements ProcessorInterface
{
    private static int $count = 0;

    public function process(ItemInterface $item): ItemInterface
    {
        self::$count++;

        return $item;
    }

    public static function count(): int
    {
        return self::$count;
    }
}
