<?php

// CPT for storing audit reports
add_action('init', function () {
    register_post_type('seo_report', [
        'label' => 'SEO Reports',
        'public' => false,
        'show_ui' => true,
        'has_archive' => false,
        'rewrite' => false,
        'supports' => ['title', 'custom-fields'], // ✅ this enables meta fields
        'capability_type' => 'post',
        'menu_icon' => 'dashicons-chart-bar',
    ]);
});

// CPT per salvare i lead da form opt-in (guide, strumenti, ecc.)
function riseup_register_lead_optins_cpt() {
    $labels = [
        'name'               => 'Lead Opt-ins',
        'singular_name'      => 'Lead Opt-in',
        'menu_name'          => 'Lead Opt-ins',
        'add_new'            => 'Aggiungi nuovo',
        'add_new_item'       => 'Aggiungi nuovo Lead',
        'edit_item'          => 'Modifica Lead',
        'view_item'          => 'Visualizza Lead',
        'all_items'          => 'Tutti i Lead',
        'search_items'       => 'Cerca Lead',
        'not_found'          => 'Nessun lead trovato',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-email-alt2',
        'supports'           => ['title'],
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 26
    ];

    register_post_type('lead_optins', $args);
}
add_action('init', 'riseup_register_lead_optins_cpt');

?>