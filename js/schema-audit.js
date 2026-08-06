// schema-audit.js — mismo flujo que SEO audit (doble opt-in)
// URL + Email obligatorios → confirmación por email → análisis en background

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('schema_audit_form');
  if (!form) return;

  form.setAttribute('novalidate', 'novalidate');

  const siteInput   = form.querySelector('[name="form_fields[site_url_sch]"]');
  const emailInput  = form.querySelector('[name="form_fields[email_sch]"]');
  const responseBox = document.getElementById('schema-audit-response') ||
    (() => {
      const box = document.createElement('div');
      box.id = 'schema-audit-response';
      box.className = 'schema-audit-response';
      form.appendChild(box);
      return box;
    })();
  const submitButton = form.querySelector('button[type="submit"]');

  if (!siteInput || !emailInput || !submitButton) return;

  submitButton.setAttribute('type', 'button');
  emailInput.removeAttribute('required');

  submitButton.addEventListener('click', function () {
    if (submitButton.disabled) return;

    let siteUrl = (siteInput.value || '').trim();
    const email = (emailInput.value || '').trim();

    // Validación: AMBOS campos son obligatorios
    if (!siteUrl) {
      responseBox.className = 'schema-error';
      responseBox.innerText = 'Inserisci un sito web valido.';
      return;
    }
    if (!email) {
      responseBox.className = 'schema-error';
      responseBox.innerText = 'Inserisci un indirizzo email valido.';
      return;
    }
    if (!/^https?:\/\//i.test(siteUrl)) siteUrl = 'https://' + siteUrl;

    responseBox.className = 'loading';
    responseBox.innerText = 'Controlla la tua email e conferma l\'indirizzo...';
    submitButton.disabled = true;

    // Paso 1: Crear CPT + pedir confirmación (doble opt-in)
    const fd = new FormData();
    fd.append('action', 'init_schema_audit');
    fd.append('site_url', siteUrl);
    fd.append('email', email);

    fetch(schemaAuditData.ajaxurl, { method: 'POST', body: fd })
      .then(r => r.json())
      .then(data => {
        if (data && data.success) {
          responseBox.className = 'schema-audit-response';
          responseBox.innerText = data.message || 'Controlla la tua email per confermare.';
          // Reset formulario
          siteInput.value = '';
          emailInput.value = '';
          submitButton.innerText = 'Analizza';
        } else {
          responseBox.className = 'schema-error';
          responseBox.innerText = (data && data.message) || 'Errore durante la richiesta.';
        }
      })
      .catch(() => {
        responseBox.className = 'schema-error';
        responseBox.innerText = 'Errore di rete.';
      })
      .finally(() => { submitButton.disabled = false; });
  }, { passive: false });
});
