import './bootstrap';

function formatMoneyBR(value) {
    const digits = value.replace(/\D/g, '');
    const number = (parseInt(digits || '0', 10) / 100).toFixed(2);
    return number.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function onMoneyInput(e) {
    const input = e.target;
    const start = input.selectionStart;
    const before = input.value;
    input.value = formatMoneyBR(input.value);
    const diff = input.value.length - before.length;
    if (typeof start === 'number') {
        input.setSelectionRange(Math.max(0, start + diff), Math.max(0, start + diff));
    }
}

document.querySelectorAll('input[data-money], textarea[data-money]').forEach((el) => {
    el.addEventListener('input', onMoneyInput);
    el.value = formatMoneyBR(el.value || '');
});
