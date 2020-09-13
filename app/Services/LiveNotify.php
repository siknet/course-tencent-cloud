<?php

namespace App\Services;

use App\Models\Chapter as ChapterModel;
use App\Models\ChapterLive as ChapterLiveModel;
use App\Repos\Chapter as ChapterRepo;

class LiveNotify extends Service
{

    public function handle()
    {
        $time = $this->request->getPost('t', 'int');
        $sign = $this->request->getPost('sign', 'string');
        $action = $this->request->getQuery('action', 'string');

        if (!$this->checkSign($sign, $time)) {
            return false;
        }

        $result = false;

        switch ($action) {
            case 'streamBegin':
                $result = $this->handleStreamBegin();
                break;
            case 'streamEnd':
                $result = $this->handleStreamEnd();
                break;
            case 'record':
                $result = $this->handleRecord();
                break;
            case 'snapshot':
                $result = $this->handleSnapshot();
                break;
            case 'porn':
                $result = $this->handlePorn();
                break;
        }

        return $result;
    }

    public function getNotifyKey()
    {
        return 'live_notify';
    }

    public function getSentNotifyKey()
    {
        return 'live_notify_sent';
    }

    /**
     * 推流
     */
    protected function handleStreamBegin()
    {
        $streamId = $this->request->getPost('stream_id', 'string');

        $chapter = $this->getChapter($streamId);

        if (!$chapter) return false;

        $attrs = $chapter->attrs;

        $attrs['stream']['status'] = ChapterModel::SS_ACTIVE;

        $chapter->update(['attrs' => $attrs]);

        $chapterLive = $this->getChapterLive($chapter->id);

        $chapterLive->update(['status' => ChapterLiveModel::STATUS_ACTIVE]);

        $this->sendBeginNotify($chapter);

        return true;
    }

    /**
     * 断流
     */
    protected function handleStreamEnd()
    {
        $streamId = $this->request->getPost('stream_id', 'string');

        $chapter = $this->getChapter($streamId);

        if (!$chapter) return false;

        $attrs = $chapter->attrs;

        $attrs['stream']['status'] = ChapterModel::SS_INACTIVE;

        $chapter->update(['attrs' => $attrs]);

        $chapterLive = $this->getChapterLive($chapter->id);

        $chapterLive->update(['status' => ChapterLiveModel::STATUS_INACTIVE]);

        return true;
    }

    /**
     * 录制
     */
    protected function handleRecord()
    {

    }

    /**
     * 截图
     */
    protected function handleSnapshot()
    {

    }

    /**
     * 鉴黄
     */
    protected function handlePorn()
    {

    }

    protected function sendBeginNotify(ChapterModel $chapter)
    {
        $cache = $this->getCache();

        $redis = $this->getRedis();

        $key = $this->getNotifyKey();

        $redis->sAdd($key, $chapter->id);

        $redis->expire($key, 86400);
    }

    protected function getChapter($streamId)
    {
        $id = (int)str_replace('chapter_', '', $streamId);

        $chapterRepo = new ChapterRepo();

        return $chapterRepo->findById($id);
    }

    protected function getChapterLive($chapterId)
    {
        $chapterRepo = new ChapterRepo();

        return $chapterRepo->findChapterLive($chapterId);
    }

    /**
     * 检查签名
     *
     * @param string $sign
     * @param int $time
     * @return bool
     */
    protected function checkSign($sign, $time)
    {
        if (empty($sign) || empty($time)) {
            return false;
        }

        if ($time < time()) {
            return false;
        }

        $notify = $this->getSectionSettings('live.notify');

        $mySign = md5($notify['auth_key'] . $time);

        return $sign == $mySign;
    }

}