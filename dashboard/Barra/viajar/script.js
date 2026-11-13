document.addEventListener("DOMContentLoaded", () => {
    const radioButtons = document.querySelectorAll('.radio-input');
    const checkoutPriceText = document.getElementById('checkout-price');
    const checkoutClassText = document.getElementById('checkout-class');

    function updateCheckout() {
        const selectedRadio = document.querySelector('.radio-input:checked');
        
        if (selectedRadio && checkoutPriceText && checkoutClassText) {
            const parentLabel = selectedRadio.closest('.class-option');
            
            if (parentLabel) {
                const priceElement = parentLabel.querySelector('.class-price');
                const classElement = parentLabel.querySelector('.class-name');

                if (priceElement && classElement) {
                    checkoutPriceText.textContent = priceElement.textContent.trim();
                    checkoutClassText.textContent = classElement.textContent.trim();
                }
            }
        }
    }

    radioButtons.forEach(radio => {
        radio.addEventListener('change', updateCheckout);
    });

    updateCheckout();
});