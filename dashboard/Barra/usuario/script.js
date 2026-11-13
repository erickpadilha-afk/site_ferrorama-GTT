document.addEventListener("DOMContentLoaded", () => {
    const logoutBtn = document.getElementById("logoutBtn");

    if (logoutBtn) {
        logoutBtn.addEventListener("click", (e) => {
            const confirmacao = confirm("Você tem certeza que deseja sair?\nSua sessão será cancelada.");
            
            if (!confirmacao) {
                e.preventDefault();
            }
        });
    }
});