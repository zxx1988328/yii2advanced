<?php
namespace common\services;

use frontend\models\AdNotice;
use frontend\models\Topic;

class TopicService extends PostService
{

    public function userDoAction($id, $action)
    {
        $topic = Topic::findTopic($id);
        $user = \Yii::$app->user->getIdentity();
        if (in_array($action, ['like', 'hate'])) {
            return UserService::TopicActionA($user, $topic, $action);
        } else {
            return UserService::TopicActionB($user, $topic, $action);
        }
    }

    /**
     * 撤销帖子
     * @param Topic $topic
     */
    public static function revoke(Topic $topic)
    {
        $topic->setAttributes(['post_status' => Topic::STATUS_ACTIVE]);
        $topic->save();
    }

    /**
     * 加精华
     * @param Topic $topic
     */
    public static function excellent(Topic $topic)
    {
        $action = ($topic->post_status == Topic::STATUS_ACTIVE) ? Topic::STATUS_EXCELLENT : Topic::STATUS_ACTIVE;
        $topic->setAttributes(['post_status' => $action]);
        $topic->save();
    }
}