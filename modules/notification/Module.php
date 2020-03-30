<?php

/**
 * @copyright Copyright &copy; Saeed, 2015 - 2018
 * @package   yii2-tree
 * @version   1.0.9
 */

namespace app\modules\notification;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use app\modules\notification\channels\ChannelInterface;

class Module extends \yii\base\Module
{
    const TRANSLATION_CATEGORY = 'notifications';
    /**
     * Channels configuration
     * @var array
     */
    public $channels = null;
    /**
     * In which id-component-name is queue is located
     * @var string
     */
    public $queueIn = null;
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }
    /**
     * Returns channel instance
     * @param string $channel the channel name
     * @return ChannelInterface
     * @throws InvalidConfigException
     */
    public function getChannelInstance($channel, array $params = [])
    {
        if ($this->channels === null) {
            throw new InvalidConfigException(Yii::t('notifications', 'There is no configured channels for Notifications module'));
        }
        if (!isset($this->channels[$channel])) {
            throw new InvalidConfigException(Yii::t('notifications', 'Channel "{channel}" not configured.', [
                'channel' => $channel,
            ]));
        }
        if (!$this->channels[$channel] instanceof ChannelInterface) {
            $this->channels[$channel] = \Yii::createObject(ArrayHelper::merge($this->channels[$channel], $params));
        }

        return $this->channels[$channel];
    }
    /**
     * Initializes language sources
     * @throws InvalidConfigException
     */
    public function registerTranslations()
    {
        if (!isset(Yii::$app->get('i18n')->translations[self::TRANSLATION_CATEGORY . '*'])) {
            Yii::$app->get('i18n')->translations[self::TRANSLATION_CATEGORY . '*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => __DIR__ . '/i18n',
                'sourceLanguage' => 'en'
            ];
        }
    }
}