<?php
namespace frontend\models;


use common\models\Post;
use common\models\PostTag;
use common\models\Search;
use common\services\TopicService;
use frontend\models\UserMeta;
use yii\web\NotFoundHttpException;
use Yii;

class Topic extends Post
{
    const TYPE = 'topic';

    /**
     * @var boolean CC 协议
     */
    public $cc;

    public function getLike()
    {
        $model = new UserMeta();
        return $model->isUserAction(self::TYPE, 'like', $this->meta_id);
    }

    public function getFollow()
    {
        $model = new UserMeta();
        return $model->isUserAction(self::TYPE, 'follow', $this->meta_id);
    }

    public function getHate()
    {
        $model = new UserMeta();
        return $model->isUserAction(self::TYPE, 'hate', $this->meta_id);
    }

    public function getFavorite()
    {
        $model = new UserMeta();
        return $model->isUserAction(self::TYPE, 'favorite', $this->meta_id);
    }

    public function getThanks()
    {
        $model = new UserMeta();
        return $model->isUserAction(self::TYPE, 'thanks', $this->meta_id);
    }

    /**
     * 获取关注者
     * @return static
     */
    public function getFollower()
    {
        return $this->hasMany(UserMeta::className(), ['meta_target_id' => 'post_id'])
            ->where(['meta_target_type' => self::TYPE, 'meta_type' => 'follow'])->asArray();
    }

    /**
     * 通过ID获取指定话题
     * @param $id
     * @param string $condition
     * @return array|null|\yii\db\ActiveRecord|static
     * @throws NotFoundHttpException
     */
    public static function findModel($id, $condition = '')
    {
//        if (!($model = Yii::$app->cache->get('topic' . $id))) {
        $model = static::find()
            ->where($condition)
            ->andWhere(['post_id' => $id, 'post_type' => self::TYPE])
            ->one();
//        }
        if ($model) {
//            Yii::$app->cache->set('topic' . $id, $model, 0);
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

    }

    /**
     * 通过ID获取指定话题
     * @param $id
     * @return array|Topic|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public static function findTopic($id)
    {
        return static::findModel($id, ['=', 'post_status', self::STATUS_ACTIVE]);
    }

    /**
     * 获取已经删除过的话题
     * @param $id
     * @return array|Topic|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public static function findDeletedTopic($id)
    {
        return static::findModel($id, ['=', 'post_status', self::STATUS_DELETED]);
    }

//     public function beforeSave($insert)
//     {
//         if (parent::beforeSave($insert)) {
//             pr($this->post_tags);

//             if ($this->post_tags) {
//                 $this->addTags(explode(',', $this->post_tags));
//             }
//             die('no');
//             $this->post_content = TopicService::replace($this->post_content)
//                 . ($this->cc ? t('app', 'cc {username}', ['username' => Yii::$app->user->identity->username]) : '');

//             if ($insert) {
//                 $this->post_user_id = (($this->post_user_id) ?: Yii::$app->user->id);
//                 $this->post_type = self::TYPE;
//                 $this->post_last_comment_time = $this->post_create;
//             }
//             return true;
//         } else {
//             return false;
//         }
//     }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (isset(Yii::$app->params['setting']) && Yii::$app->params['setting']['xunsearch']) {
            if ($insert) {
                $search = new Search();
                $search->topic_id = $this->id;
                $search->status = self::STATUS_ACTIVE;
            } else {
                $search = Search::findOne($this->id);
                if (!$search) {
                    // 如果立即修改 会因为在 xunsearch 找不到而不能 save
                    return false;
                }
                $search->post_status = $this->status;
            }
            $search->post_title = $this->post_title;
            $search->post_content = $this->post_content;
            $search->post_update = $this->post_update;
            $search->save();
        }
    }

    /**
     * 最后回复更新
     * @param string $username
     * @return bool
     */
    public function lastCommentToUpdate($username = '')
    {
        $this->setAttributes([
            'post_last_comment_user_name' => $username,
            'post_last_comment_time' => time()
        ]);
        return $this->save();
    }

    /**
     * 添加标签
     * @param array $tags
     * @return bool
     */
    public function addTags(array $tags)
    {
        $return = false;
        $tagItem = new PostTag();
        foreach ($tags as $tag) {
            $_tagItem = clone $tagItem;
            $tagRaw = $_tagItem::findOne(['tag_name' => $tag]);
            if (!$tagRaw) {
                $_tagItem->setAttributes([
                    'tag_name' => $tag,
                    'tag_count' => 1,
                ]);
                if ($_tagItem->save()) {
                    $return = true;
                }
            } else {
                $tagRaw->updateCounters(['tag_count' => 1]);
            }
        }
        return $return;
    }
}