<?php
/**
 * @var \Wasabi\Theme\Basic\View\BasicThemeView $this
 */

$menu = $this->Menu->render(1);

?><header id="header">
    <nav class="nav--main" role="navigation">
        <div class="container row">
            <?= $this->Html->link('Wasabi', '/', ['class' => 'logo']) ?>
            <ul><?= $menu ?></ul>
        </div>
    </nav>
    <nav class="nav--mobile" role="navigation">
        <ul><?= $menu ?></ul>
    </nav>
</header>
