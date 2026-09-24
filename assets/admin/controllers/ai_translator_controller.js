import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = { locale: String };

    connect() {
        this.addTranslateButton();
    }

    addTranslateButton() {
        if (this.element.parentNode.querySelector('.ztc-ai-trans-btn')) {
            return;
        }

        // Détection automatique de la langue de destination (valeur du champ ou langue du navigateur)
        const browserLocale = (navigator.language || navigator.userLanguage || 'en').split('-')[0];
        const targetLocale = this.hasLocaleValue ? this.localeValue : browserLocale;

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-sm btn-outline-info my-2 ztc-ai-trans-btn fw-bold d-inline-block ms-2';
        button.style.letterSpacing = '0.03em';
        button.style.zIndex = '10';
        button.innerHTML = 'Générer la traduction';

        button.addEventListener('click', (e) => {
            e.preventDefault();
            this.translateField(button, targetLocale);
        });

        if (this.element.parentNode) {
            this.element.parentNode.insertBefore(button, this.element.parentNode.firstChild);
        }
    }

    async translateField(button, targetLocale) {
        // Trouver la valeur du champ source en Français
        const fieldName = this.element.name || '';
        let sourceText = '';

        // 1. Détection ciblée par nom de champ (description ou name)
        let sourceInput = null;
        if (fieldName.includes('[description]')) {
            sourceInput = document.querySelector('textarea[name*="[translations][fr_FR][description]"], textarea[name*="[translations][fr][description]"], textarea[id*="translations_fr_FR_description"], textarea[id*="translations_fr_description"]');
        } else if (fieldName.includes('[name]')) {
            sourceInput = document.querySelector('input[name*="[translations][fr_FR][name]"], input[name*="[translations][fr][name]"], input[id*="translations_fr_FR_name"], input[id*="translations_fr_name"]');
        }

        if (!sourceInput) {
            const sourceFieldName = fieldName.replace(/\[[a-z]{2}(?:_[A-Z]{2})?\]/, '[fr_FR]').replace(/_[a-z]{2}(?:_[A-Z]{2})?_/, '_fr_FR_');
            sourceInput = document.querySelector(`[name="${sourceFieldName}"]`) 
                || document.querySelector(`[name="${sourceFieldName.replace('[fr_FR]', '[fr]')}"]`)
                || document.querySelector('textarea[name*="[fr]"][name*="metaDescription"]')
                || document.querySelector('textarea[id*="_fr_metaDescription"]')
                || document.querySelector('input[id*="fr"], textarea[id*="fr"]');
        }

        if (sourceInput && sourceInput.value) {
            sourceText = sourceInput.value;
        } else {
            // Chercher dans l'éditeur visuel WYSIWYG du bloc Français
            const frBlock = document.querySelector('[data-toggle-target="content"]') || document.querySelector('.tab-pane.active') || document.querySelector('#sylius_taxon_translations_fr_FR');
            const sourceQuill = frBlock ? frBlock.querySelector('.ql-editor') : document.querySelector('.ql-editor');
            if (sourceQuill) {
                sourceText = sourceQuill.innerHTML;
            }
        }

        if (!sourceText || sourceText.trim() === '') {
            alert('Le champ source en Français est vide.');
            return;
        }

        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> L\'IA traduit le contenu...';

        try {
            const response = await fetch('/admin/ajax/ai/translate-field', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    text: sourceText,
                    targetLocale: targetLocale,
                }),
            });

            const data = await response.json();
            if (data.success && data.translatedText) {
                // Nettoyage de sécurité JS contre les blocs markdown ```html ... ```
                let cleanedHtml = data.translatedText.trim();
                cleanedHtml = cleanedHtml.replace(/^```(?:html)?\s*/i, '').replace(/\s*```$/, '').trim();

                // Mise à jour de la valeur sous-jacente du textarea
                this.element.value = cleanedHtml;

                // Rendu visuel riche dans l'éditeur Quill WYSIWYG
                const quillEditor = this.element.parentNode.querySelector('.ql-editor');
                if (quillEditor) {
                    quillEditor.innerHTML = cleanedHtml;
                }

                this.showToast('Traduction générée et injectée avec succès !', 'success');
            } else if (data.error) {
                this.showToast('Erreur IA : ' + data.error, 'danger');
            }
        } catch (error) {
            console.error('Erreur Traduction IA:', error);
            this.showToast('Erreur lors de la traduction IA.', 'danger');
        } finally {
            button.disabled = false;
            button.innerHTML = originalHtml;
        }
    }

    showToast(message, type = 'success') {
        let container = document.getElementById('ztc-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'ztc-toast-container';
            container.style.position = 'fixed';
            container.style.top = '1.5rem';
            container.style.right = '1.5rem';
            container.style.zIndex = '99999';
            container.style.display = 'flex';
            container.style.flexDirection = 'column';
            container.style.gap = '0.5rem';
            container.style.pointerEvents = 'none';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible shadow fade show mb-0`;
        toast.style.pointerEvents = 'auto';
        toast.style.minWidth = '300px';
        toast.style.maxWidth = '420px';
        toast.style.borderRadius = '8px';
        toast.style.border = type === 'success' ? '1px solid #2fb344' : '1px solid #d63939';
        toast.style.backgroundColor = type === 'success' ? '#edfbf0' : '#fdeded';
        toast.style.color = '#1e293b';
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <span class="fs-3 me-2">${type === 'success' ? '✨' : '⚠️'}</span>
                <div>
                    <strong class="d-block text-${type === 'success' ? 'success' : 'danger'}">${type === 'success' ? 'IA ZEN TOO' : 'Erreur IA'}</strong>
                    <div class="small text-muted">${message}</div>
                </div>
                <button type="button" class="btn-close ms-auto" aria-label="Close"></button>
            </div>
        `;

        toast.querySelector('.btn-close').addEventListener('click', () => {
            toast.remove();
        });

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            toast.style.transition = 'all 0.4s ease-out';
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }
}
