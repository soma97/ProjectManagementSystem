<?php


namespace app\assets;


use yii\web\AssetBundle;

class CustomStylesAsset extends AssetBundle
{
    public $sourcePath = '@app/views/layouts';

    public $css = ['css/my_styles.css'];
}