<?php

namespace Andrijaj\DemoProject\Repositories;

use Andrijaj\DemoProject\Models\Statistics;

class StatisticsRepository
{
    private const HOME_VIEW_COUNT_ID = 1;

    /**
     * Returns the number of times the home page was viewed.
     * @return int|null
     */
    public function getHomeViewCount(): ?int
    {
        /** @var Statistics $stats */
        $stats = Statistics::query()->find(1);
        if (!$stats) {
            return null;
        }

        return $stats->HomeViewCount;
    }

    /**
     * Increments the number of times the homepage was viewed by 1.
     */
    public function incrementHomeViewCount()
    {
        Statistics::query()->where('Id', self::HOME_VIEW_COUNT_ID)->increment('HomeViewCount');
    }
}