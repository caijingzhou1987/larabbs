<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Topic;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Overtrue\Pinyin\Pinyin;

class TranslateSlug implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $topic;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Topic $topic)
    {
    // 队列任务构造器中接收了 Eloquent 模型，将会只序列化模型的 ID
        $this->topic = $topic;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 请求谷歌API接口进行翻译
        $title = $this->topic->title;
        try{
           $tr = new GoogleTranslate('en');
           $tr->setUrl('http://translate.google.cn/translate_a/single');
            $slug = $tr->translate($title);
        }catch(ErrorException $e){
            $pinyin = new Pinyin();
            $slug = $pinyin->permalink($title);
        }
        // 为了避免模型监控器死循环调用，我们使用 DB 类直接对数据库进行操作
        \DB::table('topics')->where('id',$this->topic->id)->update(['slug'=>$slug]);

    }
}
