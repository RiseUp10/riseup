<html>
<body style="font-family: Arial, sans-serif; line-height:1.5; background:#f9f9f9; color:#333;">
  <h2>Risultati dell'audit Schema Markup per <?= esc_html($site_url) ?></h2>

  <p><strong>Stato:</strong>
    <?php
      echo match($status){
        'ok'                 => '✅ Schema valido',
        'needs_optimization' => '⚠️ Schema da ottimizzare',
        default              => '❌ Nessuno schema rilevato',
      };
    ?>
  </p>

  <?php if (!empty($snippets)): ?>
    <h3>📦 Schema rilevato (estratto)</h3>
    <?php foreach ($snippets as $s): ?>
      <pre style="background:#eee; padding:10px; white-space:pre-wrap; border-radius:6px;"><?= esc_html($s) ?></pre>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if (!empty($valid)): ?>
    <h3>📘 Schema valido (parsed)</h3>
    <pre style="background:#e8f5e9; padding:10px; white-space:pre-wrap; border-radius:6px;"><?= esc_html($valid) ?></pre>
  <?php endif; ?>

  <p style="margin-top:16px;">
    👉 <a href="<?= esc_url($cta_url ?? 'https://riseup.marketing/contatto') ?>"
          style="padding:10px 14px; background:#150505; color:#fff; text-decoration:none; border-radius:6px;">
        Hai bisogno di supporto?
    </a>
  </p>
</body>
</html>
