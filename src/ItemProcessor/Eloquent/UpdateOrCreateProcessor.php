<?php

namespace Weble\DataSyncLaravel\ItemProcessor\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Weble\DataSync\Contracts\ItemInterface;
use Weble\DataSync\Contracts\ItemProcessorInterface;

class UpdateOrCreateProcessor implements ItemProcessorInterface
{
    private string $model;
    private string|array $attributes;
    private array $values;

    /**
     * @param class-string<Model> $model
     * @param string|array $attributes
     * @param array $values
     */
    public function __construct(
        string       $model,
        string|array $attributes,
        array        $values = []
    )
    {

        $this->model = $model;
        $this->attributes = $attributes;
        $this->values = $values;
    }

    public function process(ItemInterface $item): ItemInterface
    {
        /** @var class-string<Model> $class */
        $class = $this->model;

        $data = collect($item->all());

        $attributes = $data->only($this->attributes)->toArray();
        $values = $data->only($this->values)->toArray();

        $class::query()->updateOrCreate(
            $attributes,
            $values
        );

        return $item;
    }

}
