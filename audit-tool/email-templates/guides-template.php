<?php
// /wp-content/plugins/seo-audit-tool/email-templates/guides/guides-plain.php

// Variables disponibles: $data['name'], $data['sector'], $data['resource_url']
?>
Ciao <?php echo esc_html( $data['name'] ); ?>,

Come promesso, abbiamo preparato una risorsa per attività come la tua nel settore <?php echo esc_html( $data['sector'] ); ?>.
Non ti cambierà la vita, ma ti aiuterà ad avviare il processo di cambiamento necessario per la tua attività. È composta da 10 punti, ognuno dei quali contiene passaggi concreti da implementare a partire da domani.
Buona fortuna, e contattaci per qualsiasi cosa tu abbia bisogno.

Apri la guida qui:
<?php echo esc_url( $data['resource_url'] ); ?>

Buona lettura!
— Il team di Rise Up