<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function created(Reply $reply)
    {
        //回复数+1
        $topic = $reply->topic;
        $topic->increment('reply_count', 1);

        // 通知作者话题被回复了
        $topic->user->notify(new TopicReplied($reply));
    }

    public function creating(Reply $reply)
    {
        //body内容过滤防止XSS攻击
        $reply->content = clean($reply->content,'user_topic_body');
    }

    public function deleted(Reply $reply){
        //回复数-1
        $reply->topic->decrement('reply_count',1);
    }
}