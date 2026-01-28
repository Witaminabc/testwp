<?php
get_header();
?>

    <main id="primary" class="site-main">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('doctor-single'); ?>>
                <header class="doctor-header">
                    <h1 class="doctor-title"><?php the_title(); ?></h1>
                    <div class="doctor-meta">
                        <?php
                        $specialization_list = get_the_term_list(get_the_ID(), 'specialization', '<span class="specialization">', ', ', '</span>');
                        $city_list = get_the_term_list(get_the_ID(), 'city', '<span class="city">', ', ', '</span>');

                        if ($specialization_list) {
                            echo '<div class="taxonomy specialization-list">Специализация: ' . $specialization_list . '</div>';
                        }
                        if ($city_list) {
                            echo '<div class="taxonomy city-list">Город: ' . $city_list . '</div>';
                        }
                        ?>
                    </div>
                </header>

                <div class="doctor-content-wrapper">
                    <aside class="doctor-sidebar">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="doctor-image">
                                <?php the_post_thumbnail('large', ['class' => 'doctor-thumbnail']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="doctor-meta-fields">
                            <?php
                            $experience = get_field('doctor_experience');
                            if (!empty($experience)) {
                                echo '<div class="meta-field experience"><strong>Стаж:</strong> ' . esc_html($experience) . ' лет</div>';
                            }

                            $price = get_field('doctor_price');
                            if (!empty($price)) {
                                $formatted_price = number_format((float) $price, 0, '', ' ');
                                echo '<div class="meta-field price"><strong>Стоимость приёма от:</strong> ' . esc_html($formatted_price) . ' руб.</div>';
                            }

                            $rating = get_field('doctor_rating');
                            if (!empty($rating) && $rating !== '0') {
                                echo '<div class="meta-field rating"><strong>Рейтинг:</strong> ';
                                for ($i = 0; $i < 5; $i++) {
                                    echo $i < $rating ? '★' : '☆';
                                }
                                echo ' (' . esc_html($rating) . '/5)</div>';
                            }
                            ?>
                        </div>
                    </aside>

                    <div class="doctor-main-content">
                        <?php if (has_excerpt()) : ?>
                            <div class="doctor-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        <?php endif; ?>

                        <div class="doctor-content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
    </main>

<?php get_footer(); ?>