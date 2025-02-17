<?php

namespace App\Observers;

use App\Elastic\SearchQuery;
use App\Singer;
use App\Song;

class SingerObserver
{
    /**
     * Handle the singer "created" event.
     *
     * @param  \App\Singer  $singer
     * @return void
     */
    public function created(Singer $singer)
    {
        SearchQuery::saveItem($singer->id, 'singer', $singer);
    }

    /**
     * Handle the singer "updated" event.
     *
     * @param  \App\Singer  $singer
     * @return void
     */
    public function updated(Singer $singer)
    {
        SearchQuery::saveItem($singer->id, 'singer', $singer);

        Singer::deleteCacheBySlug($singer->slug);

        if ($singer->status == 1) {
            Song::where(['singer_id' => $singer->id, 'status' => Song::STATUS_SINGER_PASSIVE])
                ->update(['status' => Song::STATUS_ACTIVE]);
        }
        // We should do the direct opposite the previous one.
        elseif ($singer->status == 0) {
            Song::where(['singer_id' => $singer->id, 'status' => Song::STATUS_ACTIVE])
                ->update(['status' => Song::STATUS_SINGER_PASSIVE]);
        }
    }

    /**
     * Handle the singer "deleted" event.
     *
     * @param  \App\Singer  $singer
     * @return void
     */
    public function deleted(Singer $singer)
    {
        SearchQuery::deleteItem($singer->id, 'singer');
    }

    /**
     * Handle the singer "restored" event.
     *
     * @param  \App\Singer  $singer
     * @return void
     */
    public function restored(Singer $singer)
    {
        SearchQuery::saveItem($singer->id, 'singer', $singer);
    }

    /**
     * Handle the singer "force deleted" event.
     *
     * @param  \App\Singer  $singer
     * @return void
     */
    public function forceDeleted(Singer $singer)
    {
        SearchQuery::deleteItem($singer->id, 'singer');
    }
}
