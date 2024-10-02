<?php
function generate_author_schema_markup() {
    // Проверяем, если это одиночный пост или страница
    if (is_single() || is_page()) {
        // Получаем ID автора
        $author_id = get_post_field('post_author', get_the_ID());
        
        // Проверяем, что ID автора получен корректно
        if ($author_id) {
            // Получение данных автора
            $author_name = get_the_author_meta('display_name', $author_id);
            $author_description = get_the_author_meta('description', $author_id);
            $author_url = get_author_posts_url($author_id);
            $author_image = get_avatar_url($author_id); // URL изображения (аватар)
            $author_occupation = "Автор статей"; // Род занятий автора, можно настроить

            // Получение последних публикаций автора
            $args = array(
                'author' => $author_id,
                'posts_per_page' => 5 // Количество статей для отображения
            );
            $recent_posts = new WP_Query($args);

            // Список публикаций
            $works = [];
            if ($recent_posts->have_posts()) {
                while ($recent_posts->have_posts()) {
                    $recent_posts->the_post();
                    $works[] = array(
                        "@type" => "Article",
                        "name" => get_the_title(),
                        "url" => get_permalink(),
                        "datePublished" => get_the_date('Y-m-d'),
                        "publisher" => array(
                            "@type" => "Organization",
                            "name" => get_bloginfo('name') // Название сайта как издателя
                        )
                    );
                }
                wp_reset_postdata();
            }

            // Ссылки на социальные сети (здесь можете добавить реальные ссылки автора)
            $social_links = array(
                "https://www.facebook.com/ivan.ivanov",
                "https://twitter.com/ivan_ivanov",
                "https://www.linkedin.com/in/ivan-ivanov"
            );

            // Формирование JSON-LD
            $schema_data = array(
                "@context" => "https://schema.org",
                "@type" => "Person",
                "name" => $author_name,
                "image" => $author_image,
                "url" => $author_url,
                "description" => $author_description,
                "occupation" => array(
                    "@type" => "Occupation",
                    "name" => $author_occupation
                ),
                "works" => $works,
                "sameAs" => $social_links
            );

            // Преобразование массива в JSON
            echo '<script type="application/ld+json">' . json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
        } else {
            // Если автор не найден, можно вывести отладочную информацию
            echo '<!-- Автор не найден для поста или страницы -->';
        }
    }
}

// Вставка микроразметки в конец страниц и постов
add_action('wp_footer', 'generate_author_schema_markup');
?>
