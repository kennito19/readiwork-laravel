const canvas = document.createElement('canvas');
canvas.id = 'interactive-bg';
document.body.prepend(canvas);

const ctx = canvas.getContext('2d');
let particlesArray = [];

// Styles
canvas.style.position = 'fixed';
canvas.style.top = '0';
canvas.style.left = '0';
canvas.style.width = '100%';
canvas.style.height = '100%';
canvas.style.zIndex = '0'; // Same level as SVGs, behind content
canvas.style.pointerEvents = 'none';

// Resize
function resize() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
window.addEventListener('resize', resize);
resize();

// Mouse
const mouse = {
    x: null,
    y: null,
    radius: 150
}

window.addEventListener('mousemove', function (event) {
    mouse.x = event.x;
    mouse.y = event.y;
});

// Particle
class Particle {
    constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 2 + 1; // Smaller particles (Less cluttered)
        this.baseX = this.x;
        this.baseY = this.y;
        this.density = (Math.random() * 20) + 1;
        this.color = 'rgba(11, 31, 59, 0.1)'; // Navy particles (NOT green)
    }

    draw() {
        ctx.fillStyle = this.color;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.closePath();
        ctx.fill();
    }

    update() {
        let dx = mouse.x - this.x;
        let dy = mouse.y - this.y;
        let distance = Math.sqrt(dx * dx + dy * dy);
        let forceDirectionX = dx / distance;
        let forceDirectionY = dy / distance;
        let maxDistance = mouse.radius;
        let force = (maxDistance - distance) / maxDistance;
        let directionX = forceDirectionX * force * this.density;
        let directionY = forceDirectionY * force * this.density;

        if (distance < mouse.radius) {
            this.x -= directionX * 2; // Move away from mouse (Wavy push)
            this.y -= directionY * 2;
        } else {
            if (this.x !== this.baseX) {
                let dx = this.x - this.baseX;
                this.x -= dx / 15; // Return to original pos slowly
            }
            if (this.y !== this.baseY) {
                let dy = this.y - this.baseY;
                this.y -= dy / 15;
            }
        }
    }
}

// Connect (Web-like lines)
function connect() {
    let opacityValue = 1;
    for (let a = 0; a < particlesArray.length; a++) {
        for (let b = a; b < particlesArray.length; b++) {
            let distance = ((particlesArray[a].x - particlesArray[b].x) * (particlesArray[a].x - particlesArray[b].x)) +
                ((particlesArray[a].y - particlesArray[b].y) * (particlesArray[a].y - particlesArray[b].y));

            if (distance < (canvas.width / 7) * (canvas.height / 7)) {
                opacityValue = 1 - (distance / 20000);
                if (opacityValue > 0) {
                    // Green connection lines
                    ctx.strokeStyle = `rgba(11, 31, 59, ${opacityValue * 0.08})`;
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(particlesArray[a].x, particlesArray[a].y);
                    ctx.lineTo(particlesArray[b].x, particlesArray[b].y);
                    ctx.stroke();
                }
            }
        }
    }
}

function init() {
    particlesArray = [];
    let numberOfParticles = (canvas.width * canvas.height) / 12000;
    for (let i = 0; i < numberOfParticles; i++) {
        particlesArray.push(new Particle());
    }
}

function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    for (let i = 0; i < particlesArray.length; i++) {
        particlesArray[i].draw();
        particlesArray[i].update();
    }
    connect();
    requestAnimationFrame(animate);
}

init();
animate();

// --- TESTIMONIAL SLIDER LOGIC ---
let sliderInterval;

function slideTestimonials(direction) {
    const container = document.getElementById('testimonialSlider');
    if (!container) {
        console.warn("Testimonial Slider container not found!");
        return;
    }

    // Scroll 1 card width (350px card + 20px gap = 370px)
    const scrollAmount = 370 * direction;

    container.scrollBy({
        left: scrollAmount,
        behavior: 'smooth'
    });

    // Reset auto-slide timer on manual interaction
    resetAutoSlide();
}

function startAutoSlide() {
    clearInterval(sliderInterval); // Prevent multiple intervals
    sliderInterval = setInterval(() => {
        const container = document.getElementById('testimonialSlider');
        if (container) {
            // Check if we reached the end (approximate tolerance 10px)
            const maxScrollLeft = container.scrollWidth - container.clientWidth;

            if (container.scrollLeft >= maxScrollLeft - 10) {
                // Loop back to start smoothly
                container.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                // Scroll right
                container.scrollBy({ left: 370, behavior: 'smooth' });
            }
        }
    }, 4000); // 4 seconds per slide
}

function resetAutoSlide() {
    clearInterval(sliderInterval);
    startAutoSlide();
}

// Initialize Slider Immediately (Script is at end of body)
startAutoSlide();

function slideTestimonials(direction) {
    const container = document.getElementById('testimonialSlider');
    if (!container) return;

    // Scroll 1 card width (350px card + 20px gap = 370px)
    const scrollAmount = 370 * direction;
    container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    resetAutoSlide();
}

function startAutoSlide() {
    if (sliderInterval) clearInterval(sliderInterval);
    sliderInterval = setInterval(() => {
        const container = document.getElementById('testimonialSlider');
        if (container) {
            const maxScrollLeft = container.scrollWidth - container.clientWidth;
            if (container.scrollLeft >= maxScrollLeft - 10) {
                container.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                container.scrollBy({ left: 370, behavior: 'smooth' });
            }
        }
    }, 4000);
}

function resetAutoSlide() {
    clearInterval(sliderInterval);
    startAutoSlide();
}
