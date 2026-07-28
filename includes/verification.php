<?php
// Doble opt-in compartido por los 3 flujos de lead-gen (audit SEO, schema
// audit, guide opt-in): manda un mail de confirmación y no entrega el
// contenido real hasta que el dueño del email clickea el link.
//
// Cómo usarlo desde cada flujo:
//   ru_send_verification_email($post_id, $email, 'seo_audit'|'schema_audit'|'guide');
// y enganchar el trabajo real a uno de estos hooks:
//   do_action('ru_verified_seo_audit', $post_id);
//   do_action('ru_verified_schema_audit', $post_id);
//   do_action('ru_verified_guide', $post_id);

if (!defined('ABSPATH')) exit;

function ru_send_verification_email($post_id, $email, $type) {
    $token = wp_generate_password(32, false);
    update_post_meta($post_id, 'verify_token', $token);
    update_post_meta($post_id, 'verify_created', time());
    update_post_meta($post_id, 'verify_type', $type);
    update_post_meta($post_id, 'verify_status', 'pending');

    $confirm_url = add_query_arg([
        'action'  => 'ru_verify_email',
        'post_id' => $post_id,
        'token'   => $token,
    ], admin_url('admin-ajax.php'));

    $context_label = match ($type) {
        'seo_audit'    => 'audit SEO',
        'schema_audit' => 'report Schema Markup',
        'guide'        => 'guida gratuita',
        default        => 'contenuto richiesto',
    };

    riseup_send_email([
        'to'       => $email,
        'subject'  => 'Conferma il tuo indirizzo per ricevere ' . $context_label,
        'template' => 'confirm-audit',
        'data'     => [
            'confirm_url'   => $confirm_url,
            'context_label' => $context_label,
        ],
        'format'   => 'html',
    ]);
}

add_action('wp_ajax_ru_verify_email', 'ru_verify_email');
add_action('wp_ajax_nopriv_ru_verify_email', 'ru_verify_email');

function ru_verify_email() {
    $post_id = absint($_GET['post_id'] ?? 0);
    $token   = sanitize_text_field($_GET['token'] ?? '');
    $landing = home_url('/email-confirmed/');

    $go = function ($status) use ($landing) {
        wp_safe_redirect(add_query_arg('status', $status, $landing));
        exit;
    };

    if (!$post_id || !$token) {
        $go('invalid');
    }

    $verify_status = get_post_meta($post_id, 'verify_status', true);
    if ($verify_status === 'confirmed') {
        $go('used');
    }

    $stored_token = get_post_meta($post_id, 'verify_token', true);
    if (!$stored_token || !hash_equals($stored_token, $token)) {
        $go('invalid');
    }

    $created = (int) get_post_meta($post_id, 'verify_created', true);
    if ($created && (time() - $created) > 2 * DAY_IN_SECONDS) {
        update_post_meta($post_id, 'verify_status', 'expired');
        $go('expired');
    }

    $type = get_post_meta($post_id, 'verify_type', true);
    update_post_meta($post_id, 'verify_status', 'confirmed');
    delete_post_meta($post_id, 'verify_token');

    register_shutdown_function(function () use ($post_id, $type) {
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        do_action('ru_verified_' . $type, $post_id);
    });

    $go('ok');
}

// Página /email-confirmed/ (a crear en Elementor): insertar este shortcode
// donde se quiera mostrar el mensaje según ?status=ok|used|expired|invalid.
add_shortcode('ru_audit_status', function () {
    $status = sanitize_text_field($_GET['status'] ?? '');
    $messages = [
        'ok'      => 'Grazie! Il contenuto richiesto ti arriverà a breve via email.',
        'used'    => 'Questo link è già stato confermato. Controlla la tua casella di posta.',
        'expired' => 'Questo link è scaduto. Richiedilo nuovamente dal sito.',
        'invalid' => 'Link non valido.',
    ];
    $text = $messages[$status] ?? $messages['invalid'];
    return '<p class="ru-audit-status">' . esc_html($text) . '</p>';
});
