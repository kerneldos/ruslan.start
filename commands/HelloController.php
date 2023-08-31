<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\AiCategory;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        $client = new Client([
            'requestConfig' => [
                'format' => Client::FORMAT_JSON,
            ],
            'baseUrl' => 'http://ai/',
        ]);

        $request = $client->post('get_text_category', [
            'text' => base64_encode('Логотипы играют важную роль в брендинге и маркетинге, став неотъемлемой частью нашей жизни. Упаковка с логотипом – является важным средством коммуникации с потребителями. Подумайте о том, что это та самая первая точка контакта между Вашим брендом и клиентом. Качество этого контакта имеет решающее значение, поэтому необходимо убедиться, что оно будет на высоком уровне. Когда клиент покупает горячий напиток в кофе-баре, он первым делом обращает внимание на внешний вид стакана и, если дизайн может вызвать интерес и запомниться, клиент будет легко идентифицировать ваш бренд в будущем. Возможность индивидуального дизайна упаковки и нанесения логотипа – это преимущество, которое могут предоставить далеко не все поставщики упаковочных материалов. Однако, если такая опция доступна, то ее точно стоит рассмотреть. Индивидуальный дизайн может стать конкурентным преимуществом в борьбе за внимание потребителей. Компания «Реал» готова предложить своим клиентам уникальное решение для печати малых тиражей, чтобы каждый участник рынка мог попробовать продвинуть свой бренд, не только украсив упаковку, но и повышая узнаваемость своего продукта. Минимальные тиражи для заказа логотипированной продукции: Бумажные стаканы – от 2500 шт. Коробки под пиццу – от 5000 шт. Компания «Реал» предлагает высококачественную печать на бумажных изделиях, а также широкие возможности в создании уникальных дизайнов, которые подчеркнут Вашу индивидуальность и помогут выделиться на фоне конкурентов. Компания «Реал» гарантирует выгодные цены при высоком уровне обслуживания и быстрой доставке, предлагая при этом индивидуальный подход к каждому клиенту и готовность рассмотреть любые пожелания и требования.'),
            'categories' => ArrayHelper::getColumn(AiCategory::find()->asArray()->all(), 'name'),
            'language' => 'ru',
        ]);
        $response = $request->send();
        echo '<pre>' . print_r($response->data['prediction'], true) . '</pre>';die('pre');

        return ExitCode::OK;
    }
}
