<?php
get_header();

$specialization_slug = isset($_GET['specialization']) ? sanitize_text_field($_GET['specialization']) : '';
$city_slug = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';
$sort_type = isset($_GET['sort']) ? sanitize_key($_GET['sort']) : 'default';
$current_paged = get_query_var('paged') ? get_query_var('paged') : 1;

$specializations = get_terms(array('taxonomy' => 'specialization', 'hide_empty' => true));
$cities = get_terms(array('taxonomy' => 'city', 'hide_empty' => true));
?>

    <main id="primary" class="site-main archive-doctors">
        <header class="page-header">
            <h1 class="page-title"><?php post_type_archive_title('Наши врачи'); ?></h1>
            <?php
            $post_type_obj = get_post_type_object('doctors');
            if (!empty($post_type_obj->description)) {
                echo '<div class="archive-description">' . wpautop($post_type_obj->description) . '</div>';
            }
            ?>
        </header>

        <form method="get" class="doctors-filters" action="<?php echo esc_url(get_post_type_archive_link('doctors')); ?>">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="specialization">Специализация:</label>
                    <select name="specialization" id="specialization" class="filter-select">
                        <option value="">Все специализации</option>
                        <?php foreach ($specializations as $spec) : ?>
                            <option value="<?php echo esc_attr($spec->slug); ?>" <?php selected($specialization_slug, $spec->slug); ?>>
                                <?php echo esc_html($spec->name); ?> (<?php echo $spec->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="city">Город:</label>
                    <select name="city" id="city" class="filter-select">
                        <option value="">Все города</option>
                        <?php foreach ($cities as $city) : ?>
                            <option value="<?php echo esc_attr($city->slug); ?>" <?php selected($city_slug, $city->slug); ?>>
                                <?php echo esc_html($city->name); ?> (<?php echo $city->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="sort">Сортировка:</label>
                    <select name="sort" id="sort" class="filter-select">
                        <option value="default" <?php selected($sort_type, 'default'); ?>>По умолчанию</option>
                        <option value="rating_desc" <?php selected($sort_type, 'rating_desc'); ?>>По рейтингу</option>
                        <option value="price_asc" <?php selected($sort_type, 'price_asc'); ?>>По цене</option>
                        <option value="experience_desc" <?php selected($sort_type, 'experience_desc'); ?>>По стажу</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="filter-submit">Применить фильтры</button>
                    <?php
                    $reset_url = get_post_type_archive_link('doctors');
                    if ($current_paged > 1) {
                        $reset_url = add_query_arg('paged', $current_paged, $reset_url);
                    }
                    ?>
                    <a href="<?php echo esc_url($reset_url); ?>" class="filter-reset">Сбросить</a>
                </div>
            </div>
        </form>

        <?php if (have_posts()) : ?>
            <div class="doctors-results-info">
                <p>Найдено врачей: <strong><?php global $wp_query; echo $wp_query->found_posts; ?></strong></p>
                <?php if (!empty($specialization_slug) || !empty($city_slug) || $sort_type != 'default') : ?>
                    <div class="active-filters">
                        <strong>Активные фильтры:</strong>
                        <?php
                        if (!empty($specialization_slug)) {
                            $spec_term = get_term_by('slug', $specialization_slug, 'specialization');
                            if ($spec_term) {
                                echo '<span class="active-filter">Специализация: ' . esc_html($spec_term->name) . '</span>';
                            }
                        }
                        if (!empty($city_slug)) {
                            $city_term = get_term_by('slug', $city_slug, 'city');
                            if ($city_term) {
                                echo '<span class="active-filter">Город: ' . esc_html($city_term->name) . '</span>';
                            }
                        }
                        if ($sort_type != 'default') {
                            $sort_labels = ['rating_desc' => 'По рейтингу', 'price_asc' => 'По цене', 'experience_desc' => 'По стажу'];
                            echo '<span class="active-filter">Сортировка: ' . $sort_labels[$sort_type] . '</span>';
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="doctors-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('doctor-card'); ?>>
                        <div class="doctor-card__thumbnail">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                    <?php the_post_thumbnail('medium', ['class' => 'doctor-card__image']); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/images/doctor-placeholder.jpg"
                                         alt="<?php the_title_attribute(); ?>" class="doctor-card__image">
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="doctor-card__content">
                            <h2 class="doctor-card__title">
                                <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                            </h2>

                            <?php
                            $doctor_specializations = get_the_terms(get_the_ID(), 'specialization');
                            if ($doctor_specializations && !is_wp_error($doctor_specializations)) :
                                $specializations_limited = array_slice($doctor_specializations, 0, 2);
                                ?>
                                <div class="doctor-card__specialization">
                                    <?php foreach ($specializations_limited as $spec) : ?>
                                        <span class="specialization-tag"><?php echo esc_html($spec->name); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="doctor-card__meta">
                                <?php
                                $experience = get_field('doctor_experience');
                                if (!empty($experience)) : ?>
                                    <div class="doctor-card__meta-item experience">
                                        <span class="meta-label">Стаж:</span>
                                        <span class="meta-value"><?php echo esc_html($experience); ?> лет</span>
                                    </div>
                                <?php endif; ?>

                                <?php
                                $price = get_field('doctor_price');
                                if (!empty($price)) :
                                    $formatted_price = number_format((float) $price, 0, '', ' ');
                                    ?>
                                    <div class="doctor-card__meta-item price">
                                        <span class="meta-label">Цена от:</span>
                                        <span class="meta-value"><?php echo esc_html($formatted_price); ?> руб.</span>
                                    </div>
                                <?php endif; ?>

                                <?php
                                $rating = get_field('doctor_rating');
                                if (!empty($rating) && $rating !== '0') : ?>
                                    <div class="doctor-card__meta-item rating">
                                        <span class="meta-label">Рейтинг:</span>
                                        <span class="meta-value">
                                        <?php for ($i = 0; $i < 5; $i++) echo $i < $rating ? '★' : '☆'; ?>
                                        <span class="rating-number">(<?php echo esc_html($rating); ?>/5)</span>
                                    </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="doctor-card__footer">
                                <a href="<?php the_permalink(); ?>" class="doctor-card__link">
                                    Подробнее о враче →
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="doctors-pagination">
                <?php
                global $wp_query;

                $pagination_base = get_post_type_archive_link('doctors');

                $filter_params = array();
                if (!empty($specialization_slug)) $filter_params['specialization'] = $specialization_slug;
                if (!empty($city_slug)) $filter_params['city'] = $city_slug;
                if ($sort_type != 'default') $filter_params['sort'] = $sort_type;

                if (!empty($filter_params)) {
                    $pagination_base = add_query_arg($filter_params, $pagination_base);
                }

                $pagination_base = remove_query_arg('paged', $pagination_base);
                $pagination_base = str_replace('?&', '?', $pagination_base);
                $pagination_base = rtrim($pagination_base, '?');

                $paginate_args = array(
                    'base'    => $pagination_base . '%_%',
                    'format'  => '?paged=%#%',
                    'current' => max(1, $current_paged),
                    'total'   => $wp_query->max_num_pages,
                    'prev_text' => '« Назад',
                    'next_text' => 'Вперед »',
                    'mid_size' => 2,
                );

                if ($current_paged <= 1 && empty($filter_params)) {
                    $paginate_args['base'] = get_post_type_archive_link('doctors') . '%_%';
                    $paginate_args['format'] = '?paged=%#%';
                }

                echo paginate_links($paginate_args);
                ?>
            </div>

        <?php else : ?>
            <section class="no-doctors">
                <p>Врачей по заданным фильтрам не найдено.</p>
                <?php
                $back_url = get_post_type_archive_link('doctors');
                if ($current_paged > 1) {
                    $back_url = add_query_arg('paged', $current_paged, $back_url);
                }
                ?>
                <p><a href="<?php echo esc_url($back_url); ?>" class="button">Вернуться к списку</a></p>
            </section>
        <?php endif; ?>

    </main>

<?php get_sidebar(); get_footer(); ?>