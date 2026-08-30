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
        button.innerHTML = `Traduire vers ${targetLocale.toUpperCase()} par IA`;

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
        const sourceFieldName = fieldName.replace(`[${targetLocale}]`, '[fr]').replace(`_${targetLocale}_`, '_fr_FR_');

        let sourceText = '';
        const sourceInput = document.querySelector(`[name="${sourceFieldName}"]`) || document.querySelector('input[id*="fr"], textarea[id*="fr"]');
        if (sourceInput && sourceInput.value) {
            sourceText = sourceInput.value;
        } else {
            // Chercher dans l'éditeur visuel WYSIWYG du champ source
            const sourceQuill = document.querySelector('.tab-pane.active .ql-editor, .ql-editor');
            if (sourceQuill) {
                sourceText = sourceQuill.innerHTML;
            }
        }

        if (!sourceText || sourceText.trim() === '') {
            alert('Le champ source en Français est vide.');
            return;
        }

        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = 'Traduction IA...';

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
                // Mise à jour de la valeur sous-jacente
                this.element.value = data.translatedText;

                // Rendu visuel riche WYSIWYG sans balises brutes pour l'administrateur
                const quillEditor = this.element.parentNode.querySelector('.ql-editor');
                if (quillEditor) {
                    quillEditor.innerHTML = data.translatedText;
                }
            }
        } catch (error) {
            console.error('Erreur Traduction IA:', error);
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }
}
