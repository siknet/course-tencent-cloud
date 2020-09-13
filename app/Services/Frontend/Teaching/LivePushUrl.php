<?php

namespace App\Services\Frontend\Teaching;

use App\Services\Frontend\ChapterLiveTrait;
use App\Services\Frontend\ChapterTrait;
use App\Services\Frontend\Service as FrontendService;
use App\Services\Live as LiveService;

class LivePushUrl extends FrontendService
{

    use ChapterTrait;
    use ChapterLiveTrait;

    public function handle()
    {
        $chapterId = $this->request->getQuery('chapter_id', 'int');

        $chapter = $this->checkChapter($chapterId);

        $service = new LiveService();

        $steamName = $this->getStreamName($chapter->id);

        return $service->getPushUrl($steamName);
    }

}
