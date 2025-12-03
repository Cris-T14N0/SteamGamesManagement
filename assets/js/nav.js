// Mobile menu toggle
const createMobileToggle = () => {
    if (window.innerWidth <= 768) {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        // Create toggle button if it doesn't exist
        if (!document.querySelector('.sidebar-toggle')) {
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'sidebar-toggle';
            toggleBtn.innerHTML = '☰';
            toggleBtn.style.cssText = 'position: fixed; top: 10px; left: 10px; z-index: 1001; background: #171a21; border: none; color: #66c0f4; padding: 10px 15px; border-radius: 4px; cursor: pointer;';
            
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
            
            document.body.appendChild(toggleBtn);
        }
    }
};

createMobileToggle();
window.addEventListener('resize', createMobileToggle);