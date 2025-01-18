<?php
/**
 * News List Template for UIkit3
 * 
 * Available variables:
 * $items    - array (required) Array of items, each containing:
 *   - title    - string (required)
 *   - text     - string (required)
 *   - image    - rex_media|null (optional)
 *   - date     - string|null (optional)
 *   - url      - string|null (optional)
 * $options  - array (optional) containing:
 *   - columns  - int (default: 3) Number of columns on desktop
 *   - layout   - string (default: 'card') Layout type: 'card', 'list', 'masonry'
 *   - showDate - bool (default: true) Whether to show the date
 */

// Default options
$options = array_merge([
    'columns' => 3,
    'layout' => 'card',
    'showDate' => true
], $options ?? []);

// Validate required data
if (!isset($items) || !is_array($items)) {
    throw new \InvalidArgumentException('Items array is required');
}

// Calculate column classes based on number of columns
$columnClass = match($options['columns']) {
    1 => 'uk-width-1-1',
    2 => 'uk-width-1-1 uk-width-1-2@s',
    3 => 'uk-width-1-1 uk-width-1-2@s uk-width-1-3@m',
    4 => 'uk-width-1-1 uk-width-1-2@s uk-width-1-3@m uk-width-1-4@l',
    default => 'uk-width-1-1 uk-width-1-2@s uk-width-1-3@m'
};

?>

<?php if ($options['layout'] === 'masonry'): ?>
    
<div class="uk-grid uk-grid-match" uk-grid="masonry: true">
    <?php foreach ($items as $item): ?>
    <div class="<?= $columnClass ?>">
        <div class="uk-card uk-card-default">
            <?php if (isset($item['image']) && $item['image'] instanceof rex_media): ?>
            <div class="uk-card-media-top">
                <img src="<?= rex_media_manager::getUrl('rex_media_medium', $item['image']->getFileName()) ?>"
                     alt="<?= rex_escape($item['image']->getTitle() ?: $item['title']) ?>"
                     loading="lazy">
            </div>
            <?php endif; ?>
            
            <div class="uk-card-body">
                <?php if ($options['showDate'] && isset($item['date'])): ?>
                <p class="uk-text-meta">
                    <time datetime="<?= date('Y-m-d', strtotime($item['date'])) ?>">
                        <?= $item['date'] ?>
                    </time>
                </p>
                <?php endif; ?>

                <h3 class="uk-card-title"><?= rex_escape($item['title']) ?></h3>
                <p><?= rex_escape(truncate($item['text'], 120)) ?></p>

                <?php if (isset($item['url'])): ?>
                <p class="uk-text-right">
                    <a href="<?= $item['url'] ?>" class="uk-button uk-button-text">
                        Weiterlesen
                    </a>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php elseif ($options['layout'] === 'list'): ?>

<div class="uk-grid" uk-grid>
    <div class="uk-width-1-1">
        <?php foreach ($items as $item): ?>
        <article class="uk-margin-medium-bottom">
            <div class="uk-grid-medium" uk-grid>
                <?php if (isset($item['image']) && $item['image'] instanceof rex_media): ?>
                <div class="uk-width-1-3@s">
                    <img src="<?= rex_media_manager::getUrl('rex_media_medium', $item['image']->getFileName()) ?>"
                         alt="<?= rex_escape($item['image']->getTitle() ?: $item['title']) ?>"
                         loading="lazy">
                </div>
                <?php endif; ?>
                
                <div class="<?= isset($item['image']) ? 'uk-width-2-3@s' : 'uk-width-1-1' ?>">
                    <?php if ($options['showDate'] && isset($item['date'])): ?>
                    <p class="uk-text-meta">
                        <time datetime="<?= date('Y-m-d', strtotime($item['date'])) ?>">
                            <?= $item['date'] ?>
                        </time>
                    </p>
                    <?php endif; ?>

                    <h3><?= rex_escape($item['title']) ?></h3>
                    <p><?= rex_escape(truncate($item['text'], 200)) ?></p>

                    <?php if (isset($item['url'])): ?>
                    <p>
                        <a href="<?= $item['url'] ?>" class="uk-button uk-button-text">
                            Weiterlesen
                        </a>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</div>

<?php else: // Default card layout ?>

<div class="uk-grid uk-grid-match" uk-grid>
    <?php foreach ($items as $item): ?>
    <div class="<?= $columnClass ?>">
        <div class="uk-card uk-card-default uk-height-1-1">
            <?php if (isset($item['image']) && $item['image'] instanceof rex_media): ?>
            <div class="uk-card-media-top">
                <img src="<?= rex_media_manager::getUrl('rex_media_medium', $item['image']->getFileName()) ?>"
                     alt="<?= rex_escape($item['image']->getTitle() ?: $item['title']) ?>"
                     loading="lazy">
            </div>
            <?php endif; ?>
            
            <div class="uk-card-body">
                <?php if ($options['showDate'] && isset($item['date'])): ?>
                <p class="uk-text-meta">
                    <time datetime="<?= date('Y-m-d', strtotime($item['date'])) ?>">
                        <?= $item['date'] ?>
                    </time>
                </p>
                <?php endif; ?>

                <h3 class="uk-card-title"><?= rex_escape($item['title']) ?></h3>
                <p><?= rex_escape(truncate($item['text'], 120)) ?></p>

                <?php if (isset($item['url'])): ?>
                <p class="uk-text-right">
                    <a href="<?= $item['url'] ?>" class="uk-button uk-button-text">
                        Weiterlesen
                    </a>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<?php
// Helper function for text truncation
function truncate($text, $length = 120): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    
    return mb_substr($text, 0, mb_strrpos(mb_substr($text, 0, $length), ' ')) . '...';
}
?>
