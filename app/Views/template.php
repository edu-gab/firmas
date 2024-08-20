<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var string $locale
 */
?>
<!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
    <title><?= lang('Blog.title') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= lang('Blog.metaDescription') ?>">
    <meta name="keywords" content="<?= lang('Blog.metaKeywords') ?>">
    <meta name="author" content="includebeer.com">
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
          integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk"
          crossorigin="anonymous">
</head>

<body>

<header class="text-center mt-4">
    <h1><?= lang('Blog.title') ?></h1>
    <?php foreach ($supportedLocales as $supLocale): ?>
        <?= anchor($supLocale, lang("Blog.languageName_{$supLocale}"), ['class' => 'mx-3'])?>
    <?php endforeach; ?>
</header>

<div class="container">
    <?= $this->renderSection('main_content') ?>
</div>

<footer class="my-5">
    <p class="text-center">&copy; 2021 <?= anchor($locale, lang('Blog.title'))?></p>
</footer>

</body>
</html>
