<!-- Only display a list of results on ajax requests - no <head> etc -->
<?php if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') : ?>
    <?php if (have_posts()) : ?>
        <ul>
            <?php while (have_posts()) : the_post() ?>
                <li>
                    <a href="<?php the_permalink() ?>">
                        <?php the_post_thumbnail() ?> 
                        <?php the_title() ?>
                    </a>
                </li>
            <?php endwhile ?>
        </ul>
    <?php else : ?>
      <ul>
        <li>No results found</li>
    </ul>
    <?php endif ?>
<!-- Normal search results code -->
<?php else : ?>
    <?php  SD_SEARCH_PLUGIN_DIR . '/ult-search.php'; ?>
<?php endif ?>