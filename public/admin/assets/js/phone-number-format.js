function maskUzPhoneInput(input) {
    input.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.startsWith('998')) {
            value = value.substring(3);
        }
        let formatted = '+998';
        if (value.length > 0) {
            formatted += ' (' + value.substring(0, 2);
        }
        if (value.length >= 2) {
            formatted += ') ' + value.substring(2, 5);
        }
        if (value.length >= 5) {
            formatted += '-' + value.substring(5, 7);
        }
        if (value.length >= 7) {
            formatted += '-' + value.substring(7, 9);
        }
        e.target.value = formatted;
    });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace') {
            let val = input.value;
            if (val.endsWith('-') || val.endsWith(')') || val.endsWith('(') || val.endsWith(' ')) {
                e.preventDefault();
                input.value = val.slice(0, -1);
            }
        }
    });
}
