<?php
// Helper genérico para pegarle a la API de Anthropic (Claude). Compartido
// entre el audit SEO público y el futuro audit de prospección (ver
// includes/verification.php y el resto de flujos de ru-plugin) — no es
// específico de ninguno de los dos.

if (!defined('ABSPATH')) exit;

// ru_ai_complete($prompt, ['system' => '...', 'model' => '...', 'max_tokens' => N])
// Devuelve el texto de la respuesta, o null si falló (revisar debug.log).
function ru_ai_complete($prompt, $args = []) {
    if (!defined('ANTHROPIC_API_KEY') || !ANTHROPIC_API_KEY) {
        error_log('[RU AI] ANTHROPIC_API_KEY no configurada en wp-config.php');
        return null;
    }

    $body = [
        'model'      => $args['model'] ?? 'claude-haiku-4-5',
        'max_tokens' => $args['max_tokens'] ?? 1024,
        'messages'   => [
            ['role' => 'user', 'content' => $prompt],
        ],
    ];
    if (!empty($args['system'])) {
        $body['system'] = $args['system'];
    }

    $response = wp_remote_post('https://api.anthropic.com/v1/messages', [
        'timeout' => 30,
        'headers' => [
            'Content-Type'       => 'application/json',
            'x-api-key'          => ANTHROPIC_API_KEY,
            'anthropic-version'  => '2023-06-01',
        ],
        'body' => wp_json_encode($body),
    ]);

    if (is_wp_error($response)) {
        error_log('[RU AI] Error HTTP: ' . $response->get_error_message());
        return null;
    }

    $code = wp_remote_retrieve_response_code($response);
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($code !== 200) {
        error_log('[RU AI] Error API (' . $code . '): ' . wp_remote_retrieve_body($response));
        return null;
    }

    foreach ($data['content'] ?? [] as $block) {
        if (($block['type'] ?? '') === 'text') {
            return trim($block['text']);
        }
    }

    error_log('[RU AI] Respuesta sin bloque de texto: ' . wp_remote_retrieve_body($response));
    return null;
}
