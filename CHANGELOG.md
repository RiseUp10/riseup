# Changelog — RU Plugin

## 2026-08-06

**Email delivery fixes + API upgrade + Schema audit redesign**

- **SEO Audit**: Switched from unreliable third-party Render proxy to official Google PageSpeed Insights API (requires `GOOGLE_PSI_API_KEY` in wp-config.php). Added 3 synchronous retries with backoff to handle transient failures.
- **SEO Audit**: Fixed email template rendering (was showing raw Array dump). Rewrote template with proper variable checks, CSS styling, and prominent PSI failure warnings with retry instructions.
- **SEO Audit**: Email now only goes to requestor (removed BCC to admin).
- **Schema Audit**: Completely redesigned to match SEO flow — URL + Email mandatory upfront, email confirmation required before analysis runs. Analysis now executes in background after verification, not immediately.
- **Schema Audit**: Fixed email validation (was only checking if empty, not validating format).
- **Schema Audit**: Email now uses `riseup_send_email()` wrapper instead of raw `wp_mail()` (ensures SMTP routing).
- **Schema Audit**: If schema NOT found, no email sent (reduces noise; only sends when schema exists).

## 2026-07-29

Agregado el CPT `accademia` (blog/recursos, público) + taxonomía jerárquica
`academy_pillar` para organizarlo por pilar temático. Era código pegado
suelto (función sin el hook `init`, taxonomía registrada fuera de cualquier
hook — no llegaba a correr tal como estaba) — se corrigió al integrarlo:
se le sacó el prefijo `salvacash_` del nombre de función (se porta a RU, no
se queda con el nombre de origen) y se enganchó todo correctamente a `init`.

## 2026-07-28

Primera versión del plugin consolidado. Antes eran 3 plugins separados
(`riseup-seo-tools`, `seo-audit-tool`, `reviews`) instalados sueltos en
producción; se migró el sitio completo a Local y se fusionaron acá.

Qué cambió respecto a lo que había en producción:

- **Estructura aplanada**: nada de carpetas tipo sub-plugin — un solo
  `includes/` para toda la lógica, `email-templates/`, `js/`, `assets/`,
  `vendor/` a nivel raíz.
- **`reviews` se dio de baja** — estaba sobre-armado para simular reviews
  falsas; se va a rehacer a mano en Elementor. No se migró.
- **Audit SEO pasó a ser asíncrono**: antes el visitante esperaba hasta 3
  minutos (scrape + PageSpeed + email, todo antes de responder). Ahora
  responde al toque y el trabajo pesado corre después de cerrar la
  conexión.
- **Se sacó el guard de dominio del email** (el que exigía que el email
  "correspondiera" al sitio auditado) — era trivialmente bypasseable
  (matching por substring) y solo generaba fricción a gente real.
- **Antispam nuevo**: honeypot + rate limit (3/día por IP), en vez del
  guard de dominio.
- **Doble opt-in**: ningún flujo de lead-gen (audit SEO, audit Schema,
  guías) manda nada real hasta que el dueño del email confirma por link.
  Antes se mandaba directo.
- **Recomendaciones por IA**: se reemplazaron las "opportunities" genéricas
  de PageSpeed (texto de Google, no análisis real) por 3-4 recomendaciones
  concretas generadas con Claude Haiku 4.5, a partir de los datos reales
  del audit.
- **Timeout del proxy de PageSpeed** subido de 10s a 30s — como ahora el
  audit corre en background, puede esperar más sin costarle nada al
  visitante, y cubre mejor el cold-start del proxy en Render.
