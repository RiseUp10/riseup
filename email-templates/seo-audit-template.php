<?php
// Email template para SEO Audit Report
// Variables disponibles: $site_url, $onpage, $technical, $performance, $ai_recommendations, $psi_error, $cta_url

$site_url = isset($site_url) ? $site_url : '';
$onpage = isset($onpage) ? (array)$onpage : [];
$technical = isset($technical) ? (array)$technical : [];
$performance = isset($performance) ? (array)$performance : [];
$ai_recommendations = isset($ai_recommendations) ? $ai_recommendations : '';
$psi_error = isset($psi_error) ? $psi_error : '';
$cta_url = isset($cta_url) ? $cta_url : 'https://riseup.marketing/contatto';
?>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
h2 { color: #150505; border-bottom: 2px solid #150505; padding-bottom: 10px; }
h3 { color: #150505; margin-top: 20px; }
ul { padding-left: 20px; }
li { margin-bottom: 8px; }
.warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin: 15px 0; color: #856404; }
.cta-button { display: inline-block; padding: 12px 24px; background: #150505; color: white; text-decoration: none; border-radius: 6px; margin-top: 20px; }
</style>
</head>
<body>

<h2>Rapporto di audit SEO per <?php echo esc_html($site_url); ?></h2>

<p>Ciao,</p>
<p>Abbiamo completato l'analisi del tuo sito. Qui di seguito trovi i risultati:</p>

<?php if (!empty($onpage)): ?>
<h3>📌 Informazioni Website</h3>
<ul>
<?php foreach ($onpage as $label => $value): ?>
  <li><strong><?php echo esc_html($label); ?>:</strong> <?php echo esc_html($value); ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($technical)): ?>
<h3>⚙️ Informazioni Tecniche</h3>
<ul>
<?php foreach ($technical as $label => $value): ?>
  <li><strong><?php echo esc_html($label); ?>:</strong> <?php echo esc_html($value); ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($performance)): ?>
<h3>📊 PageSpeed Metrics</h3>
<ul>
<?php foreach ($performance as $label => $value): ?>
  <li><strong><?php echo esc_html($label); ?>:</strong> <?php echo esc_html($value); ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($psi_error) && $psi_error !== '-'): ?>
<div class="warning">
  <strong>⚠️ Attenzione:</strong> I dati sulle prestazioni (PageSpeed) non sono stati caricati questa volta. Ciò può accadere per motivi tecnici temporanei.
  <br><br>
  <strong>Cosa fare:</strong> Esegui di nuovo l'audit tra 30 minuti per ottenere il report completo con i dati di performance.
</div>
<?php endif; ?>

<?php if (!empty($ai_recommendations) && $ai_recommendations !== '-'): ?>
<h3>🚀 Cosa migliorare per prima</h3>
<p>Sulla base della nostra analisi, questi sono i punti prioritari:</p>
<ul>
<?php
$lines = array_filter(array_map(function($line) {
  return trim(preg_replace('/^[-•*]\s*/', '', trim($line)));
}, explode("\n", $ai_recommendations)));
foreach ($lines as $line):
  if (!empty($line)):
?>
  <li><?php echo esc_html($line); ?></li>
<?php
  endif;
endforeach;
?>
</ul>
<?php endif; ?>

<hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">

<div style="text-align: center;">
  <p><strong>Hai domande o vuoi affrontare questi problemi con il nostro team?</strong></p>
  <a href="<?php echo esc_url($cta_url); ?>" class="cta-button">Contattaci per una consulenza</a>
</div>

<p style="color: #666; font-size: 12px; margin-top: 30px;">
  Grazie per aver utilizzato Rise Up Consulting SEO Audit Tool.<br>
  Questo è un messaggio automatico. Non rispondere direttamente a questa email.
</p>

</body>
</html>
