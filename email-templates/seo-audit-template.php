<html>
<body style="font-family: Arial, sans-serif; line-height:1.5; background:#150505; color:#EEEBEB;">
    <h2>Rapporto di audit SEO per <?= esc_html($site_url) ?></h2>

    <h3>📌 Informazioni Website</h3>
    <ul>
        <?php foreach($onpage as $label => $value): ?>
            <li><strong><?= esc_html($label) ?>:</strong> <?= esc_html($value) ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>⚙️ Informazioni Tecniche</h3>
    <ul>
        <?php foreach($technical as $label => $value): ?>
            <li><strong><?= esc_html($label) ?>:</strong> <?= esc_html($value) ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>📊 PageSpeed Metrics</h3>
    <ul>
        <?php foreach($performance as $label => $value): ?>
            <li><strong><?= esc_html($label) ?>:</strong> <?= esc_html($value) ?></li>
        <?php endforeach; ?>
    </ul>

    <?php if (!empty($ai_recommendations) && $ai_recommendations !== '-'):
        $lines = array_filter(array_map(function ($line) {
            return trim(preg_replace('/^[-•*]\s*/', '', trim($line)));
        }, explode("\n", $ai_recommendations)));
    ?>
        <h3>🚀 Cosa migliorare per prima</h3>
        <ul>
            <?php foreach($lines as $line): ?>
                <li><?= esc_html($line) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($psi_error) && $psi_error !== '-'): ?>
        <p style="color:red;">
            ⚠️ Alcuni dati sulle prestazioni non sono stati caricati. Riceverai presto una versione aggiornata del report.
        </p>
    <?php endif; ?>

    <p>
        👉 <a href="<?= esc_url($cta_url) ?>" style="padding:10px 15px; border:1px solid #EEEBEB; background:#150505; color:#EEEBEB; text-decoration:none; border-radius:20px;">
            Vuoi risolvere qualche problema?
        </a>
    </p>

    <p>Grazie per utilizzare il nostro SEO Audit Tool!</p>
</body>
</html>
