<?php
/**
 * @var \WasabiTheme\Basic\View\BasicThemeView $this
 */

$menu = $this->Menu->render(1);

?><header id="header">
    <nav class="nav--main" role="navigation">
        <div class="container row">
            <?= $this->Html->link('Wasabi', [
                'model' => 'Wasabi/Cms.Pages',
                'foreign_key' => \Wasabi\Core\Wasabi::startPage()->id,
                'language_id' => \Wasabi\Core\Wasabi::contentLanguage()->id,
                '_name' => 'wasabi'
            ], [
                'class' => 'logo'
            ]) ?>
            <ul><?= $menu ?></ul>
        </div>
    </nav>
    <nav class="nav--mobile" role="navigation">
        <ul><?= $menu ?></ul>
    </nav>
</header>
