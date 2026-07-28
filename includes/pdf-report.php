<?php
// PDF export for seo_report entries (dompdf-based).

require_once plugin_dir_path(__FILE__) . '../vendor/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

function generate_seo_audit_pdf($post_id) {
    $get = function ($key) use ($post_id) {
        return get_post_meta($post_id, $key, true) ?: '-';
    };
    
   $logo_file = WP_CONTENT_DIR . '/uploads/2024/12/Marca-blanca.png';
    $logo_base64 = base64_encode(file_get_contents($logo_file));
    $logo_src = 'data:image/png;base64,' . $logo_base64;

    $html = '
    <style>
        body { font-family: Maven Pro, sans-serif; font-size: 14px; background-color: #150505; color: #EEEBEB; padding: 30px; }
        .header { text-align: center; margin-bottom: 40px; }
        .header img { height: 60px; margin-bottom: 10px; }
        h1 { color: #EEEBEB; font-size: 24px; border-bottom: 2px solid #004480; padding-bottom: 10px; margin-top: 40px; }
        h2 { font-size: 16px; color: #EEEBEB; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #004480; padding: 8px; text-align: left; }
        th { background-color: #d8e6f0; color: #150505; }
        ul { margin: 10px 0 30px 20px; }
        .footer { margin-top: 40px; font-size: 12px; color: #EEEBEB; text-align: center; }
        .button {text-align:center; border:1px solid #EEEBEB; padding:15px 22px; border-radius:20px; color: #EEEBEB; text-decoration: none; margin-top: 20px;}
    </style>
    
    <div class="header">
        <img src="' . $logo_src . '" style="height: 60px;" />
        <p>Audit SEO realizzato per te</p>
    </div>
    
    <h1>Sito Analizzato</h1>
    <p><strong>' . esc_html($get('site_url')) . '</strong></p>
    
    <h1>Performance PageSpeed</h1>
    <table>
        <tr><th>Performance Score</th><td>' . $get('psi-performance-score') . '</td></tr>
        <tr><th>LCP (Largest Contentful Paint)</th><td>' . $get('psi-lcp') . '</td></tr>
        <tr><th>CLS (Cumulative Layout Shift)</th><td>' . $get('psi-cls') . '</td></tr>
        <tr><th>INP (Interaction to Next Paint)</th><td>' . $get('psi-inp') . '</td></tr>
        <tr><th>FCP (First Contentful Paint)</th><td>' . $get('psi-fcp') . '</td></tr>
        <tr><th>Speed Index</th><td>' . $get('psi-speed-index') . '</td></tr>
        <tr><th>TBT (Total Blocking Time)</th><td>' . $get('psi-tbt') . '</td></tr>
        <tr><th>Responsive</th><td>' . $get('psi-responsive') . '</td></tr>
    </table>
    ';
    $ai_recommendations = $get('ai-recommendations');
    if ($ai_recommendations !== '-') {
        $lines = array_filter(array_map(function ($line) {
            return trim(preg_replace('/^[-•*]\s*/', '', trim($line)));
        }, explode("\n", $ai_recommendations)));

        $html .= '<h1>Cosa Migliorare per Prima</h1><ul>';
        foreach ($lines as $line) {
            $html .= '<li>' . esc_html($line) . '</li>';
        }
        $html .= '</ul>';
    }

    $html .= '
    <h1>Controllo On-Page</h1>
    <table>
        <tr><th>Titolo</th><td>' . $get('titolo') . '</td></tr>
        <tr><th>Meta Description</th><td>' . $get('meta-description') . '</td></tr>
        <tr><th>H1</th><td>' . $get('h1') . '</td></tr>
        <tr><th>Meta Robots</th><td>' . $get('meta-robots') . '</td></tr>
        <tr><th>Canonical</th><td>' . $get('canonical') . '</td></tr>
        <tr><th>Schema Markup</th><td>' . $get('schema-markup') . '</td></tr>
        <tr><th>HTTPS</th><td>' . $get('https') . '</td></tr>
        <tr><th>Viewport</th><td>' . $get('viewport') . '</td></tr>
        <tr><th>Lingua</th><td>' . $get('lingua') . '</td></tr>
        <tr><th>Dimensione HTML</th><td>' . $get('dimensione-html') . '</td></tr>
    </table>
    <br><br>
    <a href="https://wa.link/bgrf0m" target="_blank" rel="noopener" class="button">Contattaci tramite WhatsApp</a>
    
    <div class="footer">
        Rise Up © www.riseup.marketing
    </div>
    ';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $upload_dir = wp_upload_dir();
    $pdf_path = $upload_dir['basedir'] . '/seo_reports/audit-' . $post_id . '.pdf';
    $pdf_url = $upload_dir['baseurl'] . '/seo_reports/audit-' . $post_id . '.pdf';

    if (!file_exists(dirname($pdf_path))) {
        mkdir(dirname($pdf_path), 0755, true);
    }

    file_put_contents($pdf_path, $dompdf->output());

    return $pdf_url;
};

add_action('admin_init', function () {
    if (isset($_GET['generate_audit_pdf'])) {
        $report_id = intval($_GET['generate_audit_pdf']);
        $pdf_url = generate_seo_audit_pdf($report_id);
        
        echo "<h1>PDF creato!</h1>";
        echo "<p><a href='" . esc_url($pdf_url) . "' target='_blank'>Clicca qui per aprirlo</a></p>";
        exit; // Force immediate output
    }
});

add_action('add_meta_boxes', function () {
    add_meta_box(
        'seo_report_pdf_download',
        'Scarica Report PDF',
        'riseup_render_pdf_download_box',
        'seo_report',
        'side',
        'high'
    );
});

function riseup_render_pdf_download_box($post) {
    if (!function_exists('generate_seo_audit_pdf')) {
        echo '⚠️ Generatore PDF non disponibile.';
        return;
    }

    $pdf_url = generate_seo_audit_pdf($post->ID);

    echo '<p><a href="' . esc_url($pdf_url) . '" target="_blank" class="button button-primary">📄 Scarica PDF</a></p>';
    echo '<p style="font-size:12px; color:#666;">Il file viene rigenerato ogni volta che clicchi.</p>';
}