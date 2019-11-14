<?php
declare(strict_types=1);

namespace jwhulette\filevuer\services;

use Illuminate\Support\Collection;

class ConfigurationService implements ConfigurationServiceInterface
{
    /**
     * @return Collection
     */
    public function getConnectionsList(): Collection
    {
        return collect(config('filevuer.connections'));
    }

    /**
     * @return Collection
     */
    public function getConnectionDisplayList(): Collection
    {
        return $this->getConnectionsList()->map(function ($items) {
            $list = collect();
            foreach ($items as $item) {
                if (!is_null($item['name'])) {
                    $list->push($item['name']);
                }
            }

            return $list;
        });
    }

    /**
     * @param string $name
     *
     * @return array|null
     */
    public function getSelectedConnection(string $name): ?array
    {
        $connections = $this->getConnectionsList()->map(function ($items, $key) use ($name) {
            foreach ($items as $item) {
                if ($item['name'] == $name) {
                    $item['driver'] = strtolower($key);
                    return $item;
                }
            }
        });

        return $connections->filter(function ($value) {
            return !is_null($value);
        })->first();
    }
}
