// schema-audit.js — versión final mínima
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('schema_audit_form');
  if (!form) return;

  // disable native HTML validation to avoid "not focusable" errors
  form.setAttribute('novalidate', 'novalidate');

  const siteInput   = form.querySelector('[name="form_fields[site_url_sch]"]');
  const emailInput  = form.querySelector('[name="form_fields[email_sch]"]');
  let responseBox   = document.getElementById('schema-audit-response');
  const submitButton = form.querySelector('button[type="submit"]');

  if (!siteInput || !emailInput || !submitButton) return;

  // make button non-submit so Elementor won't auto-handle it
  submitButton.setAttribute('type', 'button');

  // create response box safely at end of form
  if (!responseBox) {
    responseBox = document.createElement('div');
    responseBox.id = 'schema-audit-response';
    responseBox.className = 'schema-audit-response';
    form.appendChild(responseBox);
  }

  const emailBlock = emailInput.closest('.elementor-field-group') || emailInput;
  // email visible by default but not required
  emailBlock.style.display = 'block';
  emailInput.removeAttribute('required');

  let schemaPostID = null;
  let analysisDone = false;
  submitButton.disabled = false;

  // click handler (not submit)
  submitButton.addEventListener('click', function () {
    // prevent double clicks
    if (submitButton.disabled) return;

    let siteUrl = (siteInput.value || '').trim();
    const email = (emailInput.value || '').trim();

    if (!siteUrl) {
      responseBox.className = 'schema-error';
      responseBox.innerText = 'Inserisci un sito web valido.';
      return;
    }
    if (!/^https?:\/\//i.test(siteUrl)) siteUrl = 'https://' + siteUrl;

    responseBox.className = 'loading';
    responseBox.innerText = 'Analisi in corso...';
    submitButton.disabled = true;

    if (!analysisDone) {
      // ---- analysis only ----
      const fd = new FormData();
      fd.append('action', 'run_schema_audit');
      fd.append('site_url', siteUrl);

      fetch(schemaAuditData.ajaxurl, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          responseBox.className = 'schema-audit-response';
          if (data && data.success) {
            const msg = (data.data && data.data.message) || 'Analisi completata.';
            const pid = (data.data && data.data.post_id) || null;

            responseBox.innerText = msg;

            // always show email field and make it required to capture lead
            emailBlock.style.display = 'block';
            emailInput.setAttribute('required', 'required');

            schemaPostID = pid;
            analysisDone = true;
            submitButton.innerText = 'Ricevi il report completo';
          } else {
            responseBox.innerText = (data && data.message) || 'Errore durante l\'analisi.';
          }
        })
        .catch(() => {
          responseBox.className = 'schema-error';
          responseBox.innerText = 'Errore di rete o server.';
        })
        .finally(() => { submitButton.disabled = false; });

    } else {
      // ---- send email ----
      if (!email) {
        alert('Inserisci un indirizzo email valido.');
        submitButton.disabled = false;
        return;
      }

      const fd = new FormData();
      fd.append('action', 'send_schema_audit_email');
      fd.append('post_id', schemaPostID);
      fd.append('email', email);

      fetch(schemaAuditData.ajaxurl, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          responseBox.className = 'schema-audit-response';
          const msg = (data && data.data && data.data.message) ? data.data.message : 'Report inviato via email.';
          responseBox.innerText = msg;

          // reset to initial state (email visible but not required)
          emailInput.removeAttribute('required');
          submitButton.innerText = 'Analizza';
          analysisDone = false;
        })
        .catch(() => {
          responseBox.className = 'schema-error';
          responseBox.innerText = 'Errore durante l\'invio dell\'email.';
        })
        .finally(() => { submitButton.disabled = false; });
    }
  }, { passive: false });
});