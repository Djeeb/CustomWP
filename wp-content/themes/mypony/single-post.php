<?php get_header() ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <h1><?php the_title() ?></h1>

        <?php if(get_post_meta(get_the_ID(), SponsoMetaBox::META_KEY, true) === '1'): ?>

        <?php endif ?>
          <div class="div alert alert-info">
              Cet article est sponsorisé
          </div>
        <p>
            <img src="<?php the_post_thumbnail_url() ?>" alt="" style=width:100%; height="auto;">
        </p>
        <?php the_content() ?>
<?php endwhile;
endif; ?>

<?php get_footer() ?>