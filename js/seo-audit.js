document.addEventListener('DOMContentLoaded', function () {
  // 🔹 1. Immediately remove any existing Elementor success messages
  document.querySelectorAll('.elementor-message-success')?.forEach(el => el.remove());

  // 🔹 2. Set up MutationObserver to remove any new success messages
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (
          node.nodeType === 1 &&
          node.classList.contains('elementor-message-success')
        ) {
          node.remove();
        }
      });
    });
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });

  // 🔹 3. Handle SEO Audit Form submission
  const form = document.getElementById('seo_audit_form');
  if (!form) return;

  // Honeypot: input oculto que un humano nunca completa. Los bots que rellenan
  // todos los campos a ciegas caen acá; el server lo detecta y descarta en silencio.
  const hpField = document.createElement('input');
  hpField.type = 'text';
  hpField.name = 'hp_field';
  hpField.autocomplete = 'off';
  hpField.tabIndex = -1;
  hpField.setAttribute('aria-hidden', 'true');
  hpField.style.cssText = 'position:absolute;left:-9999px;top:-9999px;';
  form.appendChild(hpField);

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const siteUrl = form.querySelector('[name="form_fields[site_url]"]').value;
    const email = form.querySelector('[name="form_fields[email]"]').value;
    const hpValue = hpField.value;
    let responseBox = form.querySelector('#seo-audit-response');

    if (!(responseBox instanceof HTMLElement)) {
      responseBox = document.createElement('div');
      responseBox.id = 'seo-audit-response';
      form.appendChild(responseBox);
    }


    if (!siteUrl || !email) {
      alert("Compila tutti i campi come richiesto.");
      return;
    }

    /*responseBox.className = ''; // reset base class
    responseBox.classList.add('loading');
    responseBox.innerText = 'Analisi in corso...';*/
    
    responseBox.className = ''; // reset base class

    if (typeof responseBox.classList?.add === 'function') {
      responseBox.classList.add('loading');
    } else {
      console.error('⚠️ responseBox.classList.add is not a function', responseBox);
    }
    
    responseBox.innerText = 'Analisi in corso...';


    // El PHP (seo_audit_run) llama a PageSpeed por su cuenta y nunca lee
    // `psi_data`, así que este fetch desde el cliente no se usaba para nada.
    // Comentado en vez de borrado por si en algún momento hace falta mostrar
    // el resultado de PSI en el front antes de que el server lo recalcule.
    /*fetch(`https://pagespeed-proxy-node.onrender.com/?url=${encodeURIComponent(siteUrl)}`)
      .then(res => res.json())
      .then(proxyData => {
        const formData = new FormData();
        formData.append('action', 'run_seo_audit');
        formData.append('site_url', siteUrl);
        formData.append('email', email);
        formData.append('psi_data', JSON.stringify(proxyData));
        formData.append('hp_field', hpValue);

        return fetch(seoAuditData.ajaxurl, {
          method: 'POST',
          body: formData,
        });
      })*/

    const formData = new FormData();
    formData.append('action', 'run_seo_audit');
    formData.append('site_url', siteUrl);
    formData.append('email', email);
    formData.append('hp_field', hpValue);

    fetch(seoAuditData.ajaxurl, {
      method: 'POST',
      body: formData,
    })
      .then(res => res.json())
      .then(data => {
        responseBox.classList.add('seo-audit-message');
        responseBox.classList.add(data.success ? 'success' : 'error');
        responseBox.innerText = data.message || 'Audit completato.';

        // Clean up Elementor message again (just in case)
        document.querySelectorAll('.elementor-message-success')?.forEach(el => el.remove());
      })
      .catch(err => {
        console.error('Errore:', err);
        responseBox.innerText = 'Errore durante l’analisi.';
      });
  });
});
