<?php

namespace App\Observers;

use App\Models\Topic;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Overtrue\Pinyin\Pinyin;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    public function saving(Topic $topic)
    {
        $topic->body = clean($topic->body,'user_topic_body');
    	$topic->excerpt = make_excerpt($topic->body);
        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if ( ! $topic->slug) {
            try{
                $tr = new GoogleTranslate();
                $tr->setUrl('http://translate.google.cn/translate_a/single');
                $topic->slug = $tr->translate($topic->title);
            }catch(ErrorException $e){
                $pinyin = new Pinyin();
                $topic->slug = $pinyin->permalink($topic->title);
            }
        }
    }
}