<?php if (isset($nodes)): ?>
<div class="panel panel-default node-panel">
    <div class="panel-heading">
        <h3 class="panel-title text-center">节点导航</h3>
    </div>

    <div class="panel-body p0">
        <dl class="dl-horizontal node-box mb0">
            <?php foreach ($nodes as $key => $value): ?>
                <dt><?= $key ?></dt>
                <dd>
                    <ul class="list-inline">
                        <?php foreach ($value as $node): ?>
                            <li><?= \yii\helpers\Html::a($node['cat_name'], ['/topic/default/index', 'node' => $node['cat_alias']]) ?></li>
                        <?php endforeach ?>
                    </ul>
                </dd>
                <div class="divider"></div>
            <?php endforeach ?>
        </dl>
    </div>
</div>
<?php endif ?>