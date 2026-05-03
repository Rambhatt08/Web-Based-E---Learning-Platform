document.addEventListener('DOMContentLoaded', () => {
    // Smooth scrolling for navigation links
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {    
                targetSection.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    document.getElementById("year").textContent = new Date().getFullYear();

    // Simple interaction for Enrollment buttons
    const enrollBtns = document.querySelectorAll('.btn-course');
    enrollBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            alert('Please Login "Course enrollment feature coming soon!"');
        });
    });
});

