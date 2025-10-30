const AUTOCOMPLETE_SELECTOR = '[data-autocomplete]';

const debounce = (fn, delay = 220) => {
    let timeoutId;
    return function debounced(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn.apply(this, args), delay);
    };
};

class AutocompleteInput {
    constructor(input) {
        this.input = input;
        this.type = input.dataset.autocomplete || 'global';
        this.endpoint = input.dataset.autocompleteUrl;
        this.minChars = parseInt(input.dataset.autocompleteMin || '2', 10);
        this.redirect = input.dataset.autocompleteRedirect === 'true';
        this.submitOnSelect = input.dataset.autocompleteSubmit === 'true';
        this.limit = parseInt(input.dataset.autocompleteLimit || '8', 10);
        this.fillMode = input.dataset.autocompleteFill || 'value';
        this.debounceDelay = parseInt(input.dataset.autocompleteDebounce || '220', 10);
        this.suggestions = [];
        this.activeIndex = -1;
        this.isLoading = false;
        this.abortController = null;
        this.panel = this.createPanel();

        if (!this.endpoint || !this.panel) {
            return;
        }

        this.bindEvents();
    }

    createPanel() {
        const parent = this.input.parentElement;
        if (!parent) {
            return null;
        }

        if (!parent.classList.contains('relative')) {
            parent.classList.add('relative');
        }

    const panel = document.createElement('div');
    panel.className = 'autocomplete-panel hidden absolute left-0 top-full z-50 mt-2 w-full max-h-80 overflow-y-auto rounded-2xl border border-[#E2E8F0] bg-white shadow-xl';
        panel.setAttribute('role', 'listbox');
        panel.setAttribute('aria-expanded', 'false');
        parent.appendChild(panel);

        return panel;
    }

    bindEvents() {
        this.input.setAttribute('autocomplete', 'off');

        const onInput = debounce(() => this.handleInput(), this.debounceDelay);
        this.input.addEventListener('input', onInput);

        this.input.addEventListener('focus', () => {
            if (this.suggestions.length && this.input.value.trim().length >= this.minChars) {
                this.showPanel();
            }
        });

        this.input.addEventListener('keydown', (event) => this.handleKeydown(event));

        this.input.addEventListener('blur', () => {
            setTimeout(() => this.hidePanel(), 150);
        });
    }

    async handleInput() {
        const term = this.input.value.trim();

        if (term.length < this.minChars) {
            this.resetSuggestions();
            this.hidePanel();
            this.cancelPendingRequest();
            return;
        }

        this.cancelPendingRequest();
        this.isLoading = true;
        this.renderPanel();

        const searchParams = new URLSearchParams({
            q: term,
            type: this.type,
            limit: String(this.limit),
        });

        this.abortController = new AbortController();

        try {
            const response = await fetch(`${this.endpoint}?${searchParams.toString()}`, {
                signal: this.abortController.signal,
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`Autocomplete request failed with status ${response.status}`);
            }

            const payload = await response.json();
            const data = Array.isArray(payload?.data) ? payload.data : [];

            this.suggestions = data;
            this.activeIndex = data.length ? 0 : -1;
            this.isLoading = false;
            this.renderPanel();

            this.showPanel();
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error('Autocomplete fetch failed', error);
            this.isLoading = false;
            this.suggestions = [];
            this.activeIndex = -1;
            this.renderPanel('Terjadi kesalahan saat mengambil data.');
            this.showPanel();
        }
    }

    handleKeydown(event) {
        if (!this.panel || this.panel.classList.contains('hidden')) {
            return;
        }

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                this.moveActive(1);
                break;
            case 'ArrowUp':
                event.preventDefault();
                this.moveActive(-1);
                break;
            case 'Enter':
                if (this.activeIndex >= 0 && this.suggestions[this.activeIndex]) {
                    event.preventDefault();
                    this.selectSuggestion(this.activeIndex);
                }
                break;
            case 'Escape':
                this.hidePanel();
                break;
            default:
                break;
        }
    }

    moveActive(delta) {
        if (!this.suggestions.length) {
            return;
        }

        const nextIndex = this.activeIndex + delta;
        if (nextIndex < 0) {
            this.activeIndex = this.suggestions.length - 1;
        } else if (nextIndex >= this.suggestions.length) {
            this.activeIndex = 0;
        } else {
            this.activeIndex = nextIndex;
        }

        this.highlightActive();
    }

    highlightActive() {
        if (!this.panel) {
            return;
        }

        this.panel.querySelectorAll('[data-autocomplete-index]').forEach((element) => {
            const index = Number(element.getAttribute('data-autocomplete-index'));
            if (index === this.activeIndex) {
                element.classList.add('bg-[#EEF2FF]', 'text-[#1D4ED8]');
                element.scrollIntoView({ block: 'nearest' });
            } else {
                element.classList.remove('bg-[#EEF2FF]', 'text-[#1D4ED8]');
            }
        });
    }

    selectSuggestion(index) {
        const suggestion = this.suggestions[index];
        if (!suggestion) {
            return;
        }

        const fillValue = this.fillMode === 'label'
            ? suggestion.label
            : (suggestion.value ?? suggestion.label ?? '');

        this.input.value = fillValue;
        this.input.dataset.autocompleteSelectedId = suggestion.id ?? '';
        this.hidePanel();

        if (this.redirect && suggestion.url) {
            window.location.href = suggestion.url;
            return;
        }

        if (this.submitOnSelect && this.input.form) {
            this.input.form.requestSubmit();
        }
    }

    showPanel() {
        if (!this.panel) {
            return;
        }

        this.panel.classList.remove('hidden');
        this.panel.setAttribute('aria-expanded', 'true');
        this.highlightActive();
    }

    hidePanel() {
        if (!this.panel) {
            return;
        }

        this.panel.classList.add('hidden');
        this.panel.setAttribute('aria-expanded', 'false');
    }

    renderPanel(message = null) {
        if (!this.panel) {
            return;
        }

        this.panel.innerHTML = '';

        if (this.isLoading) {
            this.panel.appendChild(this.renderStateRow('Mencari data...'));
            return;
        }

        if (message) {
            this.panel.appendChild(this.renderStateRow(message));
            return;
        }

        if (!this.suggestions.length) {
            this.panel.appendChild(this.renderStateRow('Tidak ada hasil ditemukan.'));
            return;
        }

        const fragment = document.createDocumentFragment();

        this.suggestions.forEach((suggestion, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'flex w-full items-start gap-3 px-4 py-3 text-left text-sm text-[#0F172A] transition-colors hover:bg-[#F8FAFC]';
            button.setAttribute('data-autocomplete-index', String(index));
            button.setAttribute('role', 'option');

            button.addEventListener('mouseenter', () => {
                this.activeIndex = index;
                this.highlightActive();
            });

            button.addEventListener('mousedown', (event) => {
                // Prevent input blur before click handler runs.
                event.preventDefault();
            });

            button.addEventListener('click', () => this.selectSuggestion(index));

            const contentWrapper = document.createElement('div');
            contentWrapper.className = 'flex flex-1 flex-col gap-1';

            const titleRow = document.createElement('div');
            titleRow.className = 'flex items-center justify-between gap-2';

            const labelSpan = document.createElement('span');
            labelSpan.className = 'font-semibold';
            labelSpan.textContent = suggestion.label ?? 'Hasil';
            titleRow.appendChild(labelSpan);

            if (suggestion.type) {
                const badge = document.createElement('span');
                badge.className = 'inline-flex items-center rounded-full bg-[#EEF2FF] px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-[#2563EB]';
                badge.textContent = String(suggestion.type).replace(/_/g, ' ').toUpperCase();
                titleRow.appendChild(badge);
            }

            contentWrapper.appendChild(titleRow);

            if (suggestion.description) {
                const description = document.createElement('p');
                description.className = 'text-xs text-[#64748B]';
                description.textContent = suggestion.description;
                contentWrapper.appendChild(description);
            }

            button.appendChild(contentWrapper);

            if (suggestion.url && !this.redirect) {
                const hint = document.createElement('span');
                hint.className = 'shrink-0 text-xs font-medium text-[#94A3B8]';
                hint.textContent = 'Tekan Enter untuk pilih';
                button.appendChild(hint);
            }

            fragment.appendChild(button);
        });

        this.panel.appendChild(fragment);
        this.highlightActive();
    }

    renderStateRow(text) {
        const wrapper = document.createElement('div');
        wrapper.className = 'px-4 py-3 text-sm text-[#64748B]';
        wrapper.textContent = text;
        return wrapper;
    }

    resetSuggestions() {
        this.suggestions = [];
        this.activeIndex = -1;
        this.isLoading = false;
        this.renderPanel();
    }

    cancelPendingRequest() {
        if (this.abortController) {
            this.abortController.abort();
            this.abortController = null;
        }
    }
}

const bootstrapAutocomplete = () => {
    document.querySelectorAll(AUTOCOMPLETE_SELECTOR).forEach((element) => {
        if (!(element instanceof HTMLInputElement)) {
            return;
        }

        if (!element.dataset.autocompleteUrl) {
            console.warn('Lewati input autocomplete tanpa data-autocomplete-url', element);
            return;
        }

        if (element.dataset.autocompleteEnabled === 'true') {
            return;
        }

        element.dataset.autocompleteEnabled = 'true';
        new AutocompleteInput(element);
    });
};

document.addEventListener('DOMContentLoaded', bootstrapAutocomplete);