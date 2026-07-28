<?php
// Available: $data['name'], $data['sector'], $data['resource_url']
?>

<p>Ciao <?php echo esc_html($data['name']); ?>,</p>

<p>Come promesso, abbiamo preparato una risorsa pensata per attività come la tua nel settore <strong><?php echo esc_html($data['sector']); ?></strong>.</p>

<p>All'interno troverai idee concrete e azioni semplici per aumentare la visibilità, attrarre nuovi clienti e migliorare i risultati in modo sostenibile.</p>

<p>👇 Clicca qui per accedere subito alla guida:</p>

<p style="margin: 20px 0;">
  <a href="<?php echo esc_url($data['resource_url']); ?>"
     style="background: #1e73be; font-size: 16px; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 15px;">
     🚀 Apri la guida ora
  </a>
</p>

<p>Buona lettura!<br>
— Il team di Rise Up</p>