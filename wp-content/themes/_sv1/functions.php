<?php
// Кастомный тип записи "Доктор" с таксономиями и фильтрацией

// ==================== ПОДДЕРЖКА ТЕМЫ ====================
add_theme_support('post-thumbnails', array('post', 'page', 'doctors'));

// ==================== ТАКСОНОМИИ (до CPT) ====================
add_action('init', 'register_doctor_taxonomies', 5);
function register_doctor_taxonomies() {
    // Таксономия "Специализация" (иерархическая как рубрики)
    //    // Таксономия "Специализация" (иерархическая как рубрики)
    $spec_labels = array(
        'name'              => 'Специализации',
        'singular_name'     => 'Специализация',
        'search_items'      => 'Искать специализации',
        'all_items'         => 'Все специализации',
        'parent_item'       => 'Родительская специализация',
        'edit_item'         => 'Редактировать специализацию',
        'update_item'       => 'Обновить специализацию',
        'add_new_item'      => 'Добавить новую специализацию',
        'menu_name'         => 'Специализации',
    );

    register_taxonomy('specialization', array('doctors'), array(
        'hierarchical'      => true,
        'labels'            => $spec_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'specialization'),
        'show_in_rest'      => false,
    ));

    // Таксономия "Город" (неиерархическая как теги)
    // Выбор обоснован: для фильтрации городов иерархия обычно не нужна,
    // каждый город независим и не требует древовидной структуры
    $city_labels = array(
        'name'              => 'Города',
        'singular_name'     => 'Город',
        'search_items'      => 'Искать города',
        'all_items'         => 'Все города',
        'edit_item'         => 'Редактировать город',
        'update_item'       => 'Обновить город',
        'add_new_item'      => 'Добавить новый город',
        'menu_name'         => 'Города',
    );

    register_taxonomy('city', array('doctors'), array(
        'hierarchical'      => false,
        'labels'            => $city_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'city'),
        'show_in_rest'      => false,
    ));
}

// ==================== КАСТОМНЫЙ ТИП ЗАПИСИ ====================
add_action('init', 'register_doctors_cpt', 10);
function register_doctors_cpt() {
    $labels = array(
        'name'               => 'Доктора',
        'singular_name'      => 'Доктор',
        'menu_name'          => 'Доктора',
        'add_new'            => 'Добавить нового',
        'add_new_item'       => 'Добавить нового доктора',
        'edit_item'          => 'Редактировать доктора',
        'all_items'          => 'Все доктора',
        'search_items'       => 'Искать докторов',
        'featured_image'     => 'Фото доктора',
        'set_featured_image' => 'Установить фото доктора',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'doctors'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-id-alt',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'       => false,
        'taxonomies'         => array('specialization', 'city'),
    );

    register_post_type('doctors', $args);
}

// ==================== ОТКЛЮЧЕНИЕ GUTENBERG ====================
add_filter('use_block_editor_for_post_type', 'disable_gutenberg_for_post_types', 10, 2);
function disable_gutenberg_for_post_types($use_block_editor, $post_type) {
    if (in_array($post_type, array('post', 'doctors'))) {
        return false;
    }
    return $use_block_editor;
}



// ==================== ФИЛЬТРАЦИЯ И СОРТИРОВКА ====================
add_action('pre_get_posts', 'custom_doctors_archive_query');
function custom_doctors_archive_query($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('doctors')) {
        $specialization_slug = isset($_GET['specialization']) ? sanitize_text_field($_GET['specialization']) : '';
        $city_slug = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';
        $sort_type = isset($_GET['sort']) ? sanitize_key($_GET['sort']) : 'default';

        $query->set('posts_per_page', 9);

        // Таксономические фильтры
        $tax_conditions = array();
        if (!empty($specialization_slug)) {
            $tax_conditions[] = array(
                'taxonomy' => 'specialization',
                'field'    => 'slug',
                'terms'    => $specialization_slug,
            );
        }
        if (!empty($city_slug)) {
            $tax_conditions[] = array(
                'taxonomy' => 'city',
                'field'    => 'slug',
                'terms'    => $city_slug,
            );
        }
        if (!empty($tax_conditions)) {
            if (count($tax_conditions) > 1) {
                $tax_conditions['relation'] = 'AND';
            }
            $query->set('tax_query', $tax_conditions);
        }

        // Сортировка
        switch ($sort_type) {
            case 'rating_desc':
                $query->set('meta_key', 'doctor_rating');
                $query->set('orderby', 'meta_value_num');
                $query->set('order', 'DESC');
                break;
            case 'price_asc':
                $query->set('meta_key', 'doctor_price');
                $query->set('orderby', 'meta_value_num');
                $query->set('order', 'ASC');
                break;
            case 'experience_desc':
                $query->set('meta_key', 'doctor_experience');
                $query->set('orderby', 'meta_value_num');
                $query->set('order', 'DESC');
                break;
            default:
                $query->set('orderby', 'date');
                $query->set('order', 'DESC');
                break;
        }
    }
}

// ==================== ПОДДЕРЖКА JFIF ====================
add_filter('upload_mimes', 'add_jfif_support');
function add_jfif_support($mimes) {
    $mimes['jfif'] = 'image/jfif';
    $mimes['jfi'] = 'image/jpeg';
    $mimes['jif'] = 'image/jpeg';
    return $mimes;
}

// ==================== with dump wp_cli ====================
// ==================== without dump wp_cli, +script bat ====================

