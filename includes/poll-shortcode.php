<?php


add_action('wp_enqueue_scripts', 'custom_polls_enqueue_scripts');


function encuestas_tarjetas_shortcode($atts)
{

    $post_id = get_the_ID();

    $poll_cuestion1 = get_post_meta($post_id, 'hp_polls_cuestion1', true);
    $poll_answer1 = get_post_meta($post_id, 'hp_polls_answer1', true);
    $poll_cuestion2 = get_post_meta($post_id, 'hp_polls_cuestion2', true);
    $poll_answer2 = get_post_meta($post_id, 'hp_polls_answer2', true);
    $poll_cuestion3 = get_post_meta($post_id, 'hp_polls_cuestion3', true);
    $poll_answer3 = get_post_meta($post_id, 'hp_polls_answer3', true);
    $poll_cuestion4 = get_post_meta($post_id, 'hp_polls_cuestion4', true);
    $poll_answer4 = get_post_meta($post_id, 'hp_polls_answer4', true);


    $has_polls = false;
    $output = '<div class="poll-card__container">';


    // Cuestion 1
    if (!empty($poll_cuestion1) && !empty($poll_answer1)) {
        $has_polls = true;
        $output .= '<div class="poll-card" id="poll-card-1">';
        $output .= '<h3>' . esc_html($poll_cuestion1) . '</h3>';
        $output .= '<form class="poll-form" data-post-id="' . $post_id . '" data-poll-id="1">';
        $answers_array1 = explode(',', $poll_answer1);
        foreach ($answers_array1 as $answer) {
            $output .= '<div class="poll-answer-container">';
            $output .= '<button type="button" class="poll-answer-button" data-answer="' . esc_attr(trim($answer)) . '">' . esc_html(trim($answer)) . ' <span class="poll-answer-percentage" style="display: none;"></span></button>';
            $output .= '</div>';
        }
        $output .= '</form>';
        $output .= '<p class="total-votes-count"></p>';

        $output .= '</div>';
    }

    // Cuestion 2
    if (!empty($poll_cuestion2) && !empty($poll_answer2)) {
        $has_polls = true;
        $output .= '<div class="poll-card" id="poll-card-2">';
        $output .= '<h3>' . esc_html($poll_cuestion2) . '</h3>';
        $output .= '<form class="poll-form" data-post-id="' . $post_id . '" data-poll-id="2">';
        $answers_array2 = explode(',', $poll_answer2);
        foreach ($answers_array2 as $answer) {
            $output .= '<div class="poll-answer-container">';
            $output .= '<button type="button" class="poll-answer-button" data-answer="' . esc_attr(trim($answer)) . '">' . esc_html(trim($answer)) . ' <span class="poll-answer-percentage" style="display: none;"></span></button>';
            $output .= '</div>';
        }

        $output .= '</form>';
        $output .= '<p class="total-votes-count"></p>';
        $output .= '</div>';
        $output .= '<div class="poll-navigation" id="poll-navigation">';
        $output .= '<button class="prev-poll-button">preview</button>';
        $output .= '<button class="next-poll-button">Siguiente</button>';
        $output .= '</div>';
    }

    // Cuestion3
    if (!empty($poll_cuestion3) && !empty($poll_answer3)) {
        $has_polls = true;
        $output .= '<div class="poll-card" id="poll-card-3">';
        $output .= '<h3>' . esc_html($poll_cuestion3) . '</h3>';
        $output .= '<form class="poll-form" data-post-id="' . $post_id . '" data-poll-id="3">';
        $answers_array3 = explode(',', $poll_answer3);
        foreach ($answers_array3 as $answer) {
            $output .= '<div class="poll-answer-container">';
            $output .= '<button type="button" class="poll-answer-button" data-answer="' . esc_attr(trim($answer)) . '">' . esc_html(trim($answer)) . ' <span class="poll-answer-percentage" style="display: none;"></span></button>';
            $output .= '</div>';
        }

        $output .= '</form>';
        $output .= '<p class="total-votes-count"></p>';
        $output .= '</div>';
        $output .= '<div class="poll-navigation" id="poll-navigation">';
        $output .= '<button class="prev-poll-button">preview</button>';
        $output .= '<button class="next-poll-button">Siguiente</button>';
        $output .= '</div>';
    }
    // Cuestion4
    if (!empty($poll_cuestion4) && !empty($poll_answer4)) {
        $has_polls = true;
        $output .= '<div class="poll-card" id="poll-card-4">';
        $output .= '<h3>' . esc_html($poll_cuestion4) . '</h3>';
        $output .= '<form class="poll-form" data-post-id="' . $post_id . '" data-poll-id="4">';
        $answers_array4 = explode(',', $poll_answer4);
        foreach ($answers_array4 as $answer) {
            $output .= '<div class="poll-answer-container">';
            $output .= '<button type="button" class="poll-answer-button" data-answer="' . esc_attr(trim($answer)) . '">' . esc_html(trim($answer)) . ' <span class="poll-answer-percentage" style="display: none;"></span></button>';
            $output .= '</div>';
        }

        $output .= '</form>';
        $output .= '<p class="total-votes-count"></p>';
        $output .= '</div>';
        $output .= '<div class="poll-navigation" id="poll-navigation">';
        $output .= '<button class="prev-poll-button">preview</button>';
        $output .= '<button class="next-poll-button">Siguiente</button>';
        $output .= '</div>';
    }
    $output .= '</div>';

    if ($has_polls) {
        return $output;
    } else {
        return '<p>No polls found for this post.</p>';
    }
}
add_shortcode('encuestas_tarjetas', 'encuestas_tarjetas_shortcode');