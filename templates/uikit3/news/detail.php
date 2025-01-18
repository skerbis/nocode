<?php
/**
 * News Detail Template for UIkit3
 * 
 * Available variables:
 * $title    - string (required)
 * $text     - string (required)
 * $image    - rex_media|null (optional)
 * $date     - string|null (optional)
 * $author   - string|null (optional)
 * $category - string|null (optional)
 */

// Ensure required variables are set
if (!isset($title) || !isset($text)) {
    throw new \InvalidArgumentException('Required variables are not set');
}
?>

<article class="uk-article">
    <?php if (isset($date) || isset($author) || isset($category)): ?>
    <div class="uk-article-meta uk-margin-bottom">
        <?php 
        $meta = [];
        if (isset($date)) {
            $meta[] = '<time datetime="' . date('Y-m-d', strtotime($date)) . '">' . $date . '</time>';
        }
        if (isset($author)) {
            $meta[] = $author;
        }
        if (isset($category)) {
            $meta[] = $category;
        }
        echo implode(' | ', $meta);
        ?>
    </div>
    <?php endif; ?>

    <h1 class="uk-article-title"><?= rex_escape($title) ?></h1>

    <?php if ($image instanceof rex_media): ?>
    <div class="uk-margin">
        <?php
        // Get image dimensions
        [$width, $height] = getimagesize($image->getPath());
        $ratio = $width / $height;
        
        // Default to 16:9 if ratio can't be determined
        $ratio = $ratio ?: 16/9;
        ?>
        
        <div class="uk-position-relative uk-visible-toggle" tabindex="-1" 
             uk-lightbox="animation: slide">
            <a href="<?= $image->getUrl() ?>" 
               data-caption="<?= rex_escape($image->getTitle() ?: $title) ?>"
               class="uk-display-block">
                <img src="<?= rex_media_manager::getUrl('rex_media_large', $image->getFileName()) ?>"
                     alt="<?= rex_escape($image->getTitle() ?: $title) ?>"
                     class="uk-width-1-1"
                     loading="lazy">
            </a>
            <?php if ($image->getValue('med_description')): ?>
            <div class="uk-text-meta uk-text-center uk-margin-small-top">
                <?= rex_escape($image->getValue('med_description')) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="uk-margin-medium-bottom uk-article-content">
        <?= $text // Assuming text is already processed through textile/markitup ?>
    </div>

    <?php if (isset($gallery) && is_array($gallery) && !empty($gallery)): ?>
    <div class="uk-margin-large-top">
        <div class="uk-h3">Bildergalerie</div>
        <div class="uk-grid uk-child-width-1-2 uk-child-width-1-3@s uk-child-width-1-4@m uk-grid-match" 
             uk-grid uk-lightbox="animation: slide">
            <?php foreach ($gallery as $galleryImage): ?>
            <?php if ($galleryImage instanceof rex_media): ?>
            <div>
                <a href="<?= $galleryImage->getUrl() ?>" 
                   data-caption="<?= rex_escape($galleryImage->getTitle() ?: $title) ?>"
                   class="uk-inline-clip uk-transition-toggle">
                    <img src="<?= rex_media_manager::getUrl('rex_media_medium', $galleryImage->getFileName()) ?>"
                         alt="<?= rex_escape($galleryImage->getTitle() ?: $title) ?>"
                         class="uk-width-1-1"
                         loading="lazy">
                    <div class="uk-transition-fade uk-position-cover uk-overlay uk-overlay-primary uk-flex uk-flex-center uk-flex-middle">
                        <span uk-icon="icon: plus; ratio: 1.5"></span>
                    </div>
                </a>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($documents) && is_array($documents) && !empty($documents)): ?>
    <div class="uk-margin-large-top">
        <div class="uk-h3">Downloads</div>
        <ul class="uk-list uk-list-divider">
            <?php foreach ($documents as $document): ?>
            <?php if ($document instanceof rex_media): ?>
            <li>
                <a href="<?= $document->getUrl() ?>" 
                   class="uk-link-text" 
                   target="_blank">
                    <span uk-icon="file-pdf" class="uk-margin-small-right"></span>
                    <?= rex_escape($document->getTitle() ?: $document->getFileName()) ?>
                    <span class="uk-text-meta uk-margin-small-left">
                        (<?= $this->getSizeFormatted($document->getSize()) ?>)
                    </span>
                </a>
            </li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

</article>
