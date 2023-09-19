<?php



function custom_polls_page_content_shortcode($atts) {
    ob_start();
    
    $current_user_id = get_current_user_id();

    echo '<div class="wrap">';

    // Retrieve listings (posts) created by the current user
    $args = array(
        'post_type' => 'hp_listing', // Cambia esto por tu tipo de post personalizado
        'posts_per_page' => -1,
        'author' => $current_user_id,
    );
    
    $listings_query = new WP_Query($args);
    
    if ($listings_query->have_posts()) {
        echo '<div class="container-dashboard_polls">';
        while ($listings_query->have_posts()) {
            $listings_query->the_post();
           
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
                echo '<div class="container-user_poll">';
                echo '<h3>' . get_the_title() . '</h3>';
               
                
                // Loop through each poll and display its information
                for ($i = 1; $i <= 4; $i++) { // Puedes ajustar el número de encuestas según tus necesidades
                    $poll_question = get_post_meta(get_the_ID(), 'hp_polls_cuestion' . $i, true);
                    $poll_answers = get_post_meta(get_the_ID(), 'hp_polls_answer' . $i, true);
    
                    if (!empty($poll_question) && !empty($poll_answers)) {
                        echo '<div class="poll-card-user">';
                        echo '<input type="text" class="poll-question" value="' . esc_attr($poll_question) . '">';

    
                        echo '<ul class="poll-answers">';
                        $answers_array = explode(',', $poll_answers);
                        foreach ($answers_array as $answer) {
                            echo '<li>';
                            echo '<input type="text" value="' . esc_attr(trim($answer)) . '">';
                            echo '</li>';
                        }
                        echo '</ul>';
                        
                        // Agregar botón de eliminación
                        echo '<button class="edit-answer-button edit-button" data-post-id="' . get_the_ID() . '" data-poll-id="' . $i . '">Edit</button>';
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
    
    $content = ob_get_clean();
    return $content;
}
add_shortcode('custom_polls_page_content', 'custom_polls_page_content_shortcode');




function edit_poll_question_and_answers() {
    $post_id = intval($_POST['post_id']);
    $poll_id = intval($_POST['poll_id']);
    $edited_question = sanitize_text_field($_POST['edited_question']);
    $edited_answers = $_POST['edited_answers']; // Un array con los nuevos valores editados

    // Actualizar la pregunta en la base de datos
    
    // Actualizar las respuestas en la base de datos
    foreach ($edited_answers as $index => $edited_answer) {
        // Restar 1 al ID de encuesta para ajustar el índice
        $current_poll_id = $poll_id;
        
        update_post_meta($post_id, 'hp_polls_cuestion' . $poll_id, $edited_question);
        update_post_meta($post_id, 'hp_polls_answer' . $current_poll_id, sanitize_text_field($edited_answer));
    }

    // Devolver una respuesta si es necesario
    wp_send_json_success('Question and answers edited successfully');
}

add_action('wp_ajax_edit_poll_question_and_answers', 'edit_poll_question_and_answers');
add_action('wp_ajax_nopriv_edit_poll_question_and_answers', 'edit_poll_question_and_answers');




