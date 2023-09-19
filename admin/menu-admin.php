<?php


function encuestas_condominios_menu()
{
    add_menu_page(
        'Custom Polls',
        'Custom Polls',
        'manage_options',
        'polls-condominium',
        'polls_condominium_page'
    );

    add_submenu_page(
        'polls-condominium',
        'Create Poll',
        'Create Poll',
        'manage_options',
        'create-poll',
        'create_poll_page'
    );

    add_submenu_page(
        'polls-condominium',
        'Edit Poll',
        'Edit Poll',
        'manage_options',
        'edit-poll',
        'edit_poll_page'
    );

    add_submenu_page(
        'polls-condominium',
        'Settings',
        'Settings',
        'manage_options',
        'poll-settings',
        'poll_settings_page'
    );
}
add_action('admin_menu', 'encuestas_condominios_menu');


