import { Controller } from '@hotwired/stimulus';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

export default class extends Controller {
    static targets = ['container', 'template'];

    connect() {
        this.initExistingSections();
    }

    initExistingSections() {
        const textareas = this.element.querySelectorAll('.legal-section-content');
        textareas.forEach((textarea) => {
            this.attachWysiwyg(textarea);
        });
    }

    addSection(event) {
        event.preventDefault();
        const index = this.containerTarget.children.length + 1;

        const sectionCard = document.createElement('div');
        sectionCard.className = 'card mb-4 border-secondary shadow-sm legal-section-item';
        sectionCard.innerHTML = `
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Article / Section ${index}</span>
                <button type="button" class="btn btn-sm btn-outline-danger" data-action="click->legal-sections#removeSection">
                    Supprimer cette section
                </button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Titre de l'Article ou du Chapitre</label>
                    <input type="text" name="page[sections][${index}][title]" class="form-control" placeholder="Ex: Article ${index} - Champs d'application et Commandes">
                    <div class="form-text text-muted">Indiquez le titre ou l'intitule numerote de cet article juridique.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Contenu Juridique de cet Article</label>
                    <textarea name="page[sections][${index}][content]" class="form-control legal-section-content" rows="8"></textarea>
                    <div class="form-text text-muted">Redigez les clauses juridiques correspondant a cet article.</div>
                </div>
            </div>
        `;

        this.containerTarget.appendChild(sectionCard);
        const textarea = sectionCard.querySelector('.legal-section-content');
        if (textarea) {
            this.attachWysiwyg(textarea);
        }
    }

    removeSection(event) {
        event.preventDefault();
        const card = event.target.closest('.legal-section-item');
        if (card) {
            card.remove();
            this.updateSectionIndexes();
        }
    }

    updateSectionIndexes() {
        const items = this.containerTarget.querySelectorAll('.legal-section-item');
        items.forEach((item, idx) => {
            const headerTitle = item.querySelector('.card-header span');
            if (headerTitle) {
                headerTitle.textContent = `Article / Section ${idx + 1}`;
            }
        });
    }

    attachWysiwyg(textarea) {
        if (textarea.dataset.wysiwygInitialized) {
            return;
        }
        textarea.dataset.wysiwygInitialized = 'true';

        const wrapper = document.createElement('div');
        wrapper.style.minHeight = '240px';
        wrapper.style.background = '#ffffff';
        wrapper.style.color = '#212529';
        wrapper.style.borderRadius = '0 0 8px 8px';

        textarea.parentNode.insertBefore(wrapper, textarea);
        textarea.style.display = 'none';

        wrapper.innerHTML = textarea.value || '';

        const quill = new Quill(wrapper, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ align: [] }],
                    ['link', 'image'],
                    ['clean'],
                ],
            },
        });

        quill.on('text-change', () => {
            textarea.value = quill.root.innerHTML;
        });

        const form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', () => {
                textarea.value = quill.root.innerHTML;
            });
        }
    }
}
