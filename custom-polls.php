<?php
/*
Plugin Name: Custom Polls
Description: Grouping and displaying polls on condo rental sites
Version: 1.0
Author: Abraham Torres
*/





function custom_polls_enqueue_scripts() {
    wp_enqueue_script('custom-polls', plugin_dir_url(__FILE__) . 'custom-polls.js', array('jquery'), '1.0', true);
    wp_localize_script('custom-polls', 'custom_polls', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_style('custom-polls-styles', plugin_dir_url(__FILE__) . 'css/custom-polls.css');
}

add_action('wp_enqueue_scripts', 'custom_polls_enqueue_scripts');



require_once plugin_dir_path(__FILE__) . 'includes/polls-management.php';
require_once plugin_dir_path(__FILE__) . 'includes/poll-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'template/custom-polls-template.php';
require_once plugin_dir_path(__FILE__) . 'admin/menu-admin.php';

// Ganchos de activación y desactivación
register_activation_hook(__FILE__, 'activar_encuestas_personalizadas');
register_deactivation_hook(__FILE__, 'desactivar_encuestas_personalizadas');

//Manage votations
add_action('wp_ajax_vote_poll', 'vote_poll');
add_action('wp_ajax_nopriv_vote_poll', 'vote_poll');


function desactivar_encuestas_personalizadas() {
    // No se requieren acciones específicas al desactivar el plugin
}





function activar_encuestas_personalizadas() {
    $page_title = 'My Polls'; // Título de la página
    $page_content = '[custom_polls_page_content]'; // Contenido de la página generado por el shortcode personalizado
    $page_id = wp_insert_post(array(
        'post_title' => $page_title,
        'post_content' => $page_content,
        'post_status' => 'publish',
        'post_type' => 'page',
    ));

    update_option('custom_polls_page_id', $page_id);
}



function hide_menu_items_for_logged_out_users($items, $args) {
    if ($args->theme_location === 'header') { // Cambia 'tu-ubicacion-del-menu' por el nombre de tu ubicación de menú
        if (!is_user_logged_in()) {
            // Filtra los elementos para eliminar los que no deseas mostrar a usuarios no logeados
            $items = array_filter($items, function ($item) {
                return !strpos($item->url, '/my-polls'); // Cambia 'tu-pagina-slug' por el slug de la página que deseas ocultar
            });
        }
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'hide_menu_items_for_logged_out_users', 10, 2);





function polls_condominium_page()
{
    wp_enqueue_script('custom-polls', plugin_dir_url(__FILE__) . 'custom-polls.js', array('jquery'), '1.0', true);
    wp_localize_script('custom-polls', 'custom_polls', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_style('custom-polls-styles', plugin_dir_url(__FILE__) . 'css/custom-polls.css');



    echo '<div class="wrap">
        <h2>Polls Management</h2>';

    // Retrieve listings (posts) that have any poll metadata
    $args = array(
        'post_type' => 'hp_listing', // Cambia esto por tu tipo de post personalizado
        'posts_per_page' => -1,
    );
    
    $listings_query = new WP_Query($args);
    
    if ($listings_query->have_posts()) {
        echo '<div class="container-dashboard_polls">';
        while ($listings_query->have_posts()) {
            $listings_query->the_post();
            $poll_author = get_the_author();
            $has_polls = false; // Variable para verificar si este listado tiene encuestas
            
            // Loop through each poll and check if it has data
            for ($i = 1; $i <= 4; $i++) { // Puedes ajustar el número de encuestas según tus necesidades
                $poll_question = get_post_meta(get_the_ID(), 'hp_polls_cuestion' . $i, true);
                $poll_answers = get_post_meta(get_the_ID(), 'hp_polls_answer' . $i, true);
    
                if (!empty($poll_question) && !empty($poll_answers)) {
                    // Indica que este listado tiene al menos una encuesta
                    $has_polls = true;
                    break; // Sal del bucle si encuentras una encuesta válida
                }
            }
            
            // Muestra el listado solo si tiene encuestas
            if ($has_polls) {
                echo '<div class="container-post_polls">';
                echo '<h3>' . get_the_title() . '</h3>';
                echo '<p class="card-author"><strong>Author:</strong> ' . esc_html($poll_author) . '</p>';
                
                // Loop through each poll and display its information
                for ($i = 1; $i <= 4; $i++) { // Puedes ajustar el número de encuestas según tus necesidades
                    $poll_question = get_post_meta(get_the_ID(), 'hp_polls_cuestion' . $i, true);
                    $poll_answers = get_post_meta(get_the_ID(), 'hp_polls_answer' . $i, true);
    
                    if (!empty($poll_question) && !empty($poll_answers)) {
                        echo '<div class="poll-card_dashboard">';
                        echo '<p class="poll-question">' . esc_html($poll_question) . '</p>';
    
                        echo '<ul class="poll-answers">';
                        $answers_array = explode(',', $poll_answers);
                        foreach ($answers_array as $answer) {
                            echo '<li>' . esc_html(trim($answer)) . '<span class="poll-answer-percentage"></span></li>';
                        }
                        echo '</ul>';
    
                        // Agregar botón de eliminación
                        echo '<button class="delete-poll-button delete-button" data-post-id="' . get_the_ID() . '" data-poll-id="' . $i . '">Delete Poll</button>';
    
                        echo '</div>';
                    }
                }
                
                echo '</div>'; // Cierre del div del listado
            }
        }
    
        wp_reset_postdata();
        echo '</div>'; // Cierre del div de contenedor
    } else {
        echo '<p>No listings found.</p>';
    }
    
    

    echo '</div>';
}

add_action('wp_ajax_delete_poll', 'delete_poll'); // Para usuarios logueados
add_action('wp_ajax_nopriv_delete_poll', 'delete_poll'); // Para usuarios no logueados


function delete_poll() {
    // Recupera los parámetros de la solicitud AJAX
    $post_id = intval($_POST['post_id']);
    $poll_id = intval($_POST['poll_id']);

    // Elimina la encuesta
    delete_post_meta($post_id, 'hp_polls_cuestion' . $poll_id);
    delete_post_meta($post_id, 'hp_polls_answer' . $poll_id);
    delete_post_meta($post_id, 'hp_polls_votes' . $poll_id);

    // Actualiza el loop de encuestas después de la eliminación
    $polls = array();
    for ($i = 1; $i <= 4; $i++) {
        $poll_question = get_post_meta($post_id, 'hp_polls_cuestion' . $i, true);
        $poll_answers = get_post_meta($post_id, 'hp_polls_answer' . $i, true);

        if (!empty($poll_question) && !empty($poll_answers)) {
            $polls[$i] = array(
                'question' => $poll_question,
                'answers' => $poll_answers
            );
        }
    }

    // Actualiza los datos de encuestas en el post
    update_post_meta($post_id, 'hp_polls_data', $polls);

    // Devuelve una respuesta si es necesario
    wp_send_json_success('Poll deleted successfully');
}



function vote_poll()
{
    $post_id = intval($_POST['post_id']);
    $poll_id = intval($_POST['poll_id']);
    $selected_option = sanitize_text_field($_POST['selected_option']);


    $poll_votes_key = 'hp_polls_votes' . $poll_id;
    $poll_votes = get_post_meta($post_id, $poll_votes_key, true);

    if (!$poll_votes) {
        $poll_votes = array();
    }

    if (!isset($poll_votes[$selected_option])) {
        $poll_votes[$selected_option] = 0;
    }

    $poll_votes[$selected_option]++;
    update_post_meta($post_id, $poll_votes_key, $poll_votes);


    $total_votes = array_sum($poll_votes);
    $percentages = array();

    foreach ($poll_votes as $option => $votes) {
        $percentage = ($votes / $total_votes) * 100;
        $percentages[$option] = round($percentage, 2);
    }


    $result_data = array(
        'percentages' => $percentages,
        'total_votes' => $total_votes
    );


    echo json_encode($result_data);

    wp_die();
}
