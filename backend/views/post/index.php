<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\AdPost;

/* @var $this yii\web\View */
/* @var $searchModel common\Models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Post', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'post_id',
                'options' => ['width' => '10px'],
            ],
            'post_title',
            [
                'attribute' => 'category_name',
                'filter' => Html::activeTextInput($searchModel, 'category_name', ['class' => 'form-control']),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->category['cat_name'];
                },
            ],
            [
                'attribute' => 'username',
                'filter' => Html::activeTextInput($searchModel, 'username', ['class' => 'form-control']),
                'value' => function ($data) {
                    return $data->user['user_name'];
                },
            ],
//             'tags',
            [
                'attribute' => 'post_view_count',
                'options' => ['width' => '10px'],
            ],
            [
                'attribute' => 'post_reply_count',
                'options' => ['width' => '10px'],
            ],
//            [
//                'attribute' => 'favorite_count',
//                'options' => ['width' => '10px'],
//            ],
//            [
//                'attribute' => 'like_count',
//                'options' => ['width' => '10px'],
//            ],
//            [
//                'attribute' => 'hate_count',
//                'options' => ['width' => '10px'],
//            ],
            [
                'attribute' => 'post_order',
                'options' => ['width' => '10px'],
            ],
            [
                'class' => \common\grid\EnumColumn::className(),
                'attribute' => 'post_status',
                'filter' => AdPost::getStatuses(),
                'enum' => AdPost::getStatuses(),
            ],
            'post_update:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>