document.addEventListener('DOMContentLoaded', () => {
    const barFills = document.querySelectorAll('.chart-bar-fill');
    
    barFills.forEach(bar => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.transition = 'width 0.8s ease-out';
            bar.style.width = targetWidth;
        }, 100);
    });

    setInterval(() => {
        window.location.reload();
    }, 30000);
});