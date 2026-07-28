<?php
function send_schema_audit_email($post_id, $email) {
    $site_url   = get_post_meta($post_id, 'site_url', true);
    $status     = get_post_meta($post_id, 'schema_status', true);
    $raw        = get_post_meta($post_id, 'schema_raw', true);
    $valid_json = json_decode(get_post_meta($post_id, 'schema_valid', true), true);

    $subject = 'Schema Audit – ' . $site_url;

    ob_start();
    ?>
    <html>
    <body style="font-family: Arial, sans-serif; line-height: 1.5; background: #f9f9f9; color: #333;">
        <h2>Risultati dell'audit Schema Markup per <?php echo esc_html($site_url); ?></h2>

        <p><strong>Stato:</strong> 
        <?php
            if ($status === 'ok') echo '✅ Schema valido';
            elseif ($status === 'needs_optimization') echo '⚠️ Schema da ottimizzare';
            else echo '❌ Nessuno schema rilevato';
        ?>
        </p>

        <h3>📦 Schema Markup Grezzo</h3>
        <pre style="background:#eee; padding: 10px; white-space: pre-wrap;"><?php echo esc_html($raw); ?></pre>

        <?php if (!empty($valid_json)) : ?>
            <h3>📘 Schema Valido (estratto)</h3>
            <pre style="background:#e0f7fa; padding: 10px; white-space: pre-wrap;"><?php echo esc_html(print_r($valid_json, true)); ?></pre>
        <?php endif; ?>

        <p><br>
            👉 <a href="https://riseup.marketing/contatto" style="padding: 10px 15px; background: #150505; color: #fff; text-decoration: none; border-radius: 4px;">Hai bisogno di supporto?</a>
        </p>
    </body>
    </html>
    <?php
    $body = ob_get_clean();

    wp_mail($email, $subject, $body, ['Content-Type: text/html; charset=UTF-8']);
}
