const typeInputs = document.querySelectorAll('input[name="type"]');
const dateLabel = document.querySelector('[data-date-label]');

typeInputs.forEach((input) => {
	input.addEventListener('change', () => {
		if (dateLabel) {
			dateLabel.textContent = input.value === 'monthly' ? 'Bulan' : 'Bulan transaksi';
		}
	});
});

const chargeModal = document.querySelector('[data-charge-modal]');
const openChargeModal = document.querySelector('[data-open-charge-modal]');
const closeChargeModalButtons = document.querySelectorAll('[data-close-charge-modal]');
const setChargeModal = (isOpen) => {
	if (!chargeModal) return;
	chargeModal.classList.toggle('is-open', isOpen);
	chargeModal.setAttribute('aria-hidden', String(!isOpen));
	document.body.classList.toggle('modal-open', isOpen);
	if (isOpen) chargeModal.querySelector('input:not([type="hidden"])')?.focus();
};
openChargeModal?.addEventListener('click', () => setChargeModal(true));
closeChargeModalButtons.forEach((button) => button.addEventListener('click', () => setChargeModal(false)));

const hoverPanel = document.querySelector('[data-detail-hover]');
const hoverPanelBody = hoverPanel?.querySelector('[data-detail-hover-body]');
const hoverTriggers = document.querySelectorAll('[data-hover-detail]');

const setHoverPanel = (isOpen, event) => {
	if (!hoverPanel || !hoverPanelBody) return;

	hoverPanel.classList.toggle('is-open', isOpen);
	hoverPanel.setAttribute('aria-hidden', String(!isOpen));

	if (isOpen && event) {
		const target = event.currentTarget ?? event.target;

		if (!(target instanceof Element)) {
			return;
		}

		const rect = target.getBoundingClientRect();
		const panelWidth = Math.min(hoverPanel.offsetWidth || 420, window.innerWidth - 24);
		const left = Math.min(Math.max(rect.left + rect.width / 2 - panelWidth / 2, 12), window.innerWidth - panelWidth - 12);
		const top = Math.min(rect.bottom + 12, window.innerHeight - (hoverPanel.offsetHeight || 360) - 12);
		hoverPanel.style.left = `${left}px`;
		hoverPanel.style.top = `${top}px`;
	}
};

const loadHoverPanel = async (url, event) => {
	if (!hoverPanel || !hoverPanelBody) return;

	setHoverPanel(false, event);
	hoverPanelBody.innerHTML = '<div class="detail-loading">Memuat detail project...</div>';

	try {
		const response = await fetch(url, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
			},
		});

		if (!response.ok) {
			throw new Error('Gagal memuat detail');
		}

		const html = await response.text();
		const parser = new DOMParser();
		const doc = parser.parseFromString(html, 'text/html');
		const panelContent = doc.querySelector('main.main-content.edit-page');

		hoverPanelBody.innerHTML = panelContent ? panelContent.innerHTML : html;
		setHoverPanel(true, event);
	} catch {
		hoverPanelBody.innerHTML = '<div class="detail-loading">Gagal memuat detail project.</div>';
		setHoverPanel(true, event);
	}
};

hoverTriggers.forEach((trigger) => {
	trigger.addEventListener('mouseenter', (event) => {
		const url = trigger.dataset.hoverDetail;
		if (url) void loadHoverPanel(url, event);
	});

	trigger.addEventListener('mouseleave', () => {
		setHoverPanel(false);
	});
});

window.addEventListener('resize', () => {
	if (hoverPanel?.classList.contains('is-open')) {
		hoverPanel.style.left = '';
		hoverPanel.style.top = '';
	}
});

const sidebar = document.querySelector('.sidebar');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const sidebarToggleIcon = sidebarToggle?.querySelector('.sidebar-toggle-icon');

const setSidebarCollapsed = (isCollapsed) => {
	if (!sidebar) return;
	sidebar.classList.toggle('is-collapsed', isCollapsed);
	sidebarToggle?.setAttribute('aria-label', isCollapsed ? 'Buka sidebar' : 'Sembunyikan sidebar');
	if (sidebarToggleIcon) {
		sidebarToggleIcon.textContent = isCollapsed ? '⟩' : '⟨';
	}
};

sidebarToggle?.addEventListener('click', () => {
	const willCollapse = !sidebar.classList.contains('is-collapsed');
	setSidebarCollapsed(willCollapse);
});

const columnFilters = document.querySelectorAll('[data-column-filter]');

columnFilters.forEach((filter) => {
	const trigger = filter.querySelector('[data-filter-trigger]');
	const panel = filter.querySelector('[data-filter-panel]');

	if (!trigger || !panel) return;

	trigger.addEventListener('click', (event) => {
		event.stopPropagation();
		const isOpen = filter.classList.contains('is-open');

		columnFilters.forEach((otherFilter) => {
			if (otherFilter !== filter) {
				otherFilter.classList.remove('is-open');
			}
		});

		filter.classList.toggle('is-open', !isOpen);
	});
});

document.addEventListener('click', (event) => {
	const clickedInsideFilter = event.target.closest('[data-column-filter]');

	if (!clickedInsideFilter) {
		columnFilters.forEach((filter) => filter.classList.remove('is-open'));
	}
});

