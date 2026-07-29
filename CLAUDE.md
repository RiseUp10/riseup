# RU Plugin — contexto

Plugin custom de RiseUp Consulting para `riseup.marketing`. Consolida lo que
antes eran 3 plugins separados (`riseup-seo-tools`, `seo-audit-tool`,
`reviews` — este último murió, se rehace a mano en Elementor).

## Qué hace

- **Audit SEO público** — form en el sitio → scrapea la página + PageSpeed
  Insights → guarda como CPT `seo_report` → manda reporte por email.
- **Audit Schema Markup** — variante que solo chequea JSON-LD/microdata.
- **Lead capture de guías** — formulario Elementor Pro ("risorse") → CPT
  `lead_optins` → manda el recurso por email.
- **Recomendaciones por IA** — en vez de las "opportunities" genéricas que
  devuelve PageSpeed, se le pasan los datos del audit a Claude Haiku 4.5 y
  devuelve 3-4 recomendaciones concretas en italiano.
- **Export a PDF** — de cualquier `seo_report`, vía dompdf.
- **Accademia** — CPT `accademia` (blog/recursos educativos, público, slug
  `/accademia/`) + taxonomía jerárquica `academy_pillar` (slug `/pilastro/`)
  para organizar los artículos por pilar temático.

## Estructura

```
ru-plugin.php          bootstrap, requiere todo lo de includes/
includes/
  ai-helpers.php        ru_ai_complete() — genérico, no específico de ningún audit
  cpt-register.php      CPTs: seo_report, lead_optins
  verification.php       doble opt-in compartido (ver abajo)
  email-helpers.php      utilidades de templating de mail (rum_*)
  email-manager.php      riseup_send_email() — despachador por template
  email-report.php       email del audit SEO
  schema-email-report.php email del audit Schema
  seo-audit-core.php     AJAX handlers, scraping, PSI, cron de reintentos
  elementor-integration.php  hook del form "risorse"
  pdf-report.php          export a PDF (dompdf)
email-templates/         plantillas HTML de los mails
templates/                otras plantillas (guía en PDF, sin usar aún)
js/, assets/              front-end del audit
vendor/dompdf/             vendored a mano, no vía composer
```

## Doble opt-in (antispam)

Los 3 flujos de lead-gen (audit SEO, audit Schema, guías) comparten el mismo
mecanismo en `includes/verification.php`: no se manda nada real hasta que el
dueño del email confirma por link. `ru_send_verification_email($post_id,
$email, $type)` dispara la confirmación; el trabajo real cuelga de
`do_action('ru_verified_' . $type, $post_id)` — cada flujo engancha su
propio hook (`ru_verified_seo_audit`, `ru_verified_schema_audit`,
`ru_verified_guide`).

**Depende de una página en Elementor**: `/email-confirmed/`, con el
shortcode `[ru_audit_status]` insertado en el contenido — muestra el mensaje
correcto según `?status=ok|used|expired|invalid`. Si esa página no existe,
el redirect después de confirmar da 404.

Antes del opt-in hay honeypot (campo oculto, inyectado por JS) + rate limit
(3 audits/día por IP). Esto no evita que alguien meta el email de otra
persona — no está pensado para eso, corta bots/abuso por volumen.

## Secretos

`ANTHROPIC_API_KEY` en `wp-config.php` (fuera del repo). Sin ella,
`ru_ai_complete()` loguea el error en `debug.log` y devuelve `null` — el
audit sigue funcionando, solo sin la sección de recomendaciones.

## Deuda / pendiente

- El generador de PDF (`pdf-report.php`) y el email template
  (`seo-audit-template.php`) ya no muestran las "opportunities" crudas de
  PSI — están comentadas, no borradas, por si hace falta volver atrás.
- Idea en el radar, sin empezar: un audit general **interno** (no público)
  para prospección — el equipo corre el audit contra el dominio de un
  prospecto y manda un teaser (20% de los hallazgos) como outreach. No
  necesita el doble opt-in de los flujos públicos.
