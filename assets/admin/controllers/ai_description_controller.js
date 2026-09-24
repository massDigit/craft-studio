import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'nameSource'];

    connect() {
        this.addAiButton();
    }

    addAiButton() {
        if (this.element.parentNode.querySelector('.ztc-ai-gen-btn')) {
            return;
        }

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-sm btn-outline-warning my-2 ztc-ai-gen-btn fw-bold d-inline-block';
        button.style.letterSpacing = '0.03em';
        button.style.zIndex = '10';
        button.innerHTML = 'Générer la description';

        button.addEventListener('click', (e) => {
            e.preventDefault();
            this.generateDescription(button);
        });

        if (this.element.parentNode) {
            this.element.parentNode.insertBefore(button, this.element.parentNode.firstChild);
        }
    }

    async generateDescription(button) {
        let nameValue = '';

        // 1. Recherche dans les champs du nom de la création
        const nameInputs = document.querySelectorAll('input[id*="translations_fr_FR_name"], input[id*="translations_fr_name"], input[id*="_name"], input[name*="[name]"]');
        for (const input of nameInputs) {
            if (input && input.value && input.value.trim() !== '') {
                nameValue = input.value.trim();
                break;
            }
        }

        // 2. En mode Édition, extrait le nom depuis le titre de la page ou du fil d'Ariane
        if (!nameValue) {
            const pageHeader = document.querySelector('.page-title, h1, .breadcrumb-item.active, .card-title');
            if (pageHeader && pageHeader.textContent) {
                const headerText = pageHeader.textContent.replace('Éditer', '').replace('Edit', '').trim();
                if (headerText !== '' && !headerText.includes('Créations') && !headerText.includes('Produits') && !headerText.includes('Catalogues') && !headerText.includes('Taxons')) {
                    nameValue = headerText;
                }
            }
        }

        if (!nameValue) {
            alert('Veuillez d\'abord remplir le champ Nom.');
            return;
        }

        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> L\'IA façonne votre description...';

        try {
            // 3. Détecter si une image est présente dans le formulaire
            let imagePath = null;
            const imgEl = document.querySelector('img[src*="/media/image/"], .sylius-image img, [data-form-collection="images"] img, img.img-fluid');
            if (imgEl && imgEl.src && !imgEl.src.includes('avatar') && !imgEl.src.includes('logo')) {
                imagePath = imgEl.src;
            }

            const isTaxon = document.querySelector('form[name="sylius_taxon"], [name*="sylius_taxon"]') !== null || window.location.pathname.includes('/taxons');
            const endpoint = isTaxon ? '/admin/ajax/ai/generate-taxon-description' : '/admin/ajax/ai/generate-description';
            const payload = isTaxon ? { taxonName: nameValue, imagePath: imagePath } : { name: nameValue, imagePath: imagePath };

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            const data = await response.json();
            const content = data.description || data.content;
            if (data.success && content) {
                // Nettoyage de sécurité JS contre les blocs markdown ```html ... ```
                let cleanedHtml = content.trim();
                cleanedHtml = cleanedHtml.replace(/^```(?:html)?\s*/i, '').replace(/\s*```$/, '').trim();

                // Mise à jour de la valeur sous-jacente du textarea
                this.element.value = cleanedHtml;

                // Rendu visuel riche dans l'éditeur Quill WYSIWYG
                const quillEditor = this.element.parentNode.querySelector('.ql-editor');
                if (quillEditor) {
                    quillEditor.innerHTML = cleanedHtml;
                }

                this.showToast('Description générée et injectée avec succès !', 'success');
            } else if (data.error) {
                this.showToast('Erreur IA : ' + data.error, 'danger');
            }
        } catch (error) {
            console.error('Erreur Génération IA:', error);
            this.showToast('Erreur de communication avec le service IA.', 'danger');
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
