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
document.addEventListener('keydown', (event) => { if (event.key === 'Escape') setChargeModal(false); });
