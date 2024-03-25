<?php

namespace consumer\modules\dashboard\widgets;

use consumer\modules\dashboard\widgets\assets\ToDoListAsset;
use yii\base\Widget;

class RssNews extends Widget {
    /**
     * @return string
     */
    public function run(): string {
        $xml  = simplexml_load_string(file_get_contents('https://lenta.ru/rss/top7'), 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);
        $data = json_decode($json,true);

        return $this->render('rss-news', ['data' => $data]);
    }
}