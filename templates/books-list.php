<?php

defined('ABSPATH') || exit;

?>

<div class="wlm-books">

    <?php if ($query->have_posts()): ?>

        <?php while ($query->have_posts()): ?>

            <?php $query->the_post(); ?>

            <?php
            $year = get_post_meta(
                get_the_ID(),
                'wlm_year',
                true
            );
            ?>

            <article class="wlm-book">

                <h3>
                    <?php echo esc_html(get_the_title()); ?>
                </h3>

                <?php if ($year): ?>
                    <p>
                        Year:
                        <?php echo esc_html($year); ?>
                    </p>
                <?php endif; ?>

            </article>

        <?php endwhile; ?>

    <?php else: ?>

        <p>No books found.</p>

    <?php endif; ?>

</div>