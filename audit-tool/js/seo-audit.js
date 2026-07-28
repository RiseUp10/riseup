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

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const siteUrl = form.querySelector('[name="form_fields[site_url]"]').value;
    const email = form.querySelector('[name="form_fields[email]"]').value;
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


    fetch(`https://pagespeed-proxy-node.onrender.com/?url=${encodeURIComponent(siteUrl)}`)
      .then(res => res.json())
      .then(proxyData => {
        const formData = new FormData();
        formData.append('action', 'run_seo_audit');
        formData.append('site_url', siteUrl);
        formData.append('email', email);
        formData.append('psi_data', JSON.stringify(proxyData));

        return fetch(seoAuditData.ajaxurl, {
          method: 'POST',
          body: formData,
        });
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
