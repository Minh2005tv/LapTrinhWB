document.addEventListener('DOMContentLoaded', function () {
    // Main elements
    const slider = document.querySelector('.slider');
    const sliderContainer = document.querySelector('.slider-container');
    const dots = document.querySelectorAll('.dot');
    const slides = document.querySelectorAll('.slide');
    const loading = document.querySelector('.loading');

    // Configuration
    const slideCount = slides.length;
    let currentSlide = 0;
    const slideInterval = 4000; // Increased time between auto slides
    let autoSlideEnabled = true;
    let animationInProgress = false;

    // Touch/mouse variables
    let startX = 0;
    let startY = 0;
    let endX = 0;
    let initialTransform = 0;
    let isDragging = false;
    let isScrolling = false;
    let touchStartTime = 0;
    let touchEndTime = 0;

    // Preload images
    function preloadImages() {
        loading.style.display = 'block';
        let loadedCount = 0;

        const imageUrls = [
            './images/anh1.webp',
            './images/anh2.png'
        ];

        function checkAllLoaded() {
            loadedCount++;
            if (loadedCount >= slideCount) {
                loading.style.display = 'none';
                startSlider();
            }
        }

        for (let i = 0; i < slideCount; i++) {
            const img = new Image();
            img.onload = checkAllLoaded;
            img.onerror = checkAllLoaded; // Continue even if there's an error
            img.src = imageUrls[i];
        }
    }

    // Initialize slider
    function startSlider() {
        // Set initial position
        changeSlide(0, false);

        // Start auto-sliding
        startAutoSlide();

        // Add event listeners after images are loaded
        setupEventListeners();
    }

    // Function to change slide with optional animation
    function changeSlide(index, animate = true) {
        if (animationInProgress) return;

        // Remove active class from current slide and dot
        slides[currentSlide].classList.remove('active');
        dots[currentSlide].classList.remove('active');

        // Update current slide index with boundary checks
        currentSlide = index;
        if (currentSlide < 0) currentSlide = slideCount - 1;
        if (currentSlide >= slideCount) currentSlide = 0;

        // If animate is false, remove transition temporarily
        if (!animate) {
            sliderContainer.style.transition = 'none';
            requestAnimationFrame(() => {
                sliderContainer.style.transform = `translateX(-${currentSlide * 100 / slideCount}%)`;
                requestAnimationFrame(() => {
                    sliderContainer.style.transition = 'transform 0.8s cubic-bezier(0.215, 0.610, 0.355, 1)';
                    // Add active class immediately for no animation
                    slides[currentSlide].classList.add('active');
                    dots[currentSlide].classList.add('active');
                });
            });
        } else {
            animationInProgress = true;
            sliderContainer.style.transform = `translateX(-${currentSlide * 100 / slideCount}%)`;
            
            // Add active class with slight delay to sync with slide movement
            setTimeout(() => {
                slides[currentSlide].classList.add('active');
                dots[currentSlide].classList.add('active');
            }, 50);
            
            // Reset animation flag after transition completes
            setTimeout(() => {
                animationInProgress = false;
            }, 800); // Match transition duration
        }
    }

    // Auto slide functionality
    let slideTimer;
    function startAutoSlide() {
        if (!autoSlideEnabled) return;
        
        clearInterval(slideTimer);
        slideTimer = setInterval(() => {
            changeSlide(currentSlide + 1);
        }, slideInterval);
    }

    function resetAutoSlide() {
        if (autoSlideEnabled) {
            clearInterval(slideTimer);
            startAutoSlide();
        }
    }

    // Set up event listeners
    function setupEventListeners() {
        // Dot navigation
        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                if (animationInProgress) return;
                const slideIndex = parseInt(dot.getAttribute('data-slide'));
                changeSlide(slideIndex);
                resetAutoSlide();
            });
        });

        // Touch events for swipe functionality
        slider.addEventListener('touchstart', handleDragStart, { passive: true });
        slider.addEventListener('touchmove', handleDragMove, { passive: true });
        slider.addEventListener('touchend', handleDragEnd);

        // Mouse drag events
        slider.addEventListener('mousedown', handleDragStart);
        slider.addEventListener('mousemove', handleDragMove);
        slider.addEventListener('mouseup', handleDragEnd);
        slider.addEventListener('mouseleave', handleDragEnd);

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                if (animationInProgress) return;
                changeSlide(currentSlide - 1);
                resetAutoSlide();
            } else if (e.key === 'ArrowRight') {
                if (animationInProgress) return;
                changeSlide(currentSlide + 1);
                resetAutoSlide();
            }
        });

        // Pause auto-sliding when tab is not visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(slideTimer);
            } else {
                resetAutoSlide();
            }
        });

        // Stop auto-sliding when user interacts with slider
        slider.addEventListener('mouseenter', () => {
            clearInterval(slideTimer);
        });

        slider.addEventListener('mouseleave', () => {
            resetAutoSlide();
        });
    }

    // Handle drag start (both touch and mouse)
    function handleDragStart(e) {
        if (animationInProgress) return;
        
        const point = e.touches ? e.touches[0] : e;
        startX = point.clientX;
        startY = point.clientY;
        touchStartTime = new Date().getTime();
        
        initialTransform = parseFloat(getTransformValue()) || (-currentSlide * 100 / slideCount);
        isDragging = true;
        isScrolling = false;
        
        sliderContainer.style.transition = 'none';
        clearInterval(slideTimer);

        if (!e.touches) {
            e.preventDefault(); // Prevent text selection for mouse events
        }
    }

    // Handle drag move (both touch and mouse)
    function handleDragMove(e) {
        if (!isDragging) return;
        
        const point = e.touches ? e.touches[0] : e;
        const currentX = point.clientX;
        const currentY = point.clientY;
        
        // Detect if this is a scroll attempt
        if (!isScrolling && e.touches) {
            const diffX = Math.abs(currentX - startX);
            const diffY = Math.abs(currentY - startY);
            
            // If vertical movement is greater than horizontal, it's a scroll
            if (diffY > diffX) {
                isScrolling = true;
                isDragging = false;
                return;
            }
        }
        
        if (isScrolling) return;
        if (!e.touches) e.preventDefault();
        
        endX = currentX;
        const diffX = endX - startX;
        const movePercent = (diffX / slider.offsetWidth) * 100;
        
        const newTransform = initialTransform + movePercent;
        
        // Add resistance at edges
        let adjustedTransform = newTransform;
        const maxTransform = 0;
        const minTransform = -((slideCount - 1) * 100 / slideCount);
        
        if (newTransform > maxTransform) {
            adjustedTransform = maxTransform + (newTransform - maxTransform) * 0.3;
        } else if (newTransform < minTransform) {
            adjustedTransform = minTransform + (newTransform - minTransform) * 0.3;
        }
        
        sliderContainer.style.transform = `translateX(${adjustedTransform}%)`;
    }

    // Handle drag end (both touch and mouse)
    function handleDragEnd(e) {
        if (!isDragging) return;
        
        isDragging = false;
        touchEndTime = new Date().getTime();
        
        sliderContainer.style.transition = 'transform 0.8s cubic-bezier(0.215, 0.610, 0.355, 1)';
        
        const diffX = endX - startX;
        const timeDiff = touchEndTime - touchStartTime;
        const movePercent = (diffX / slider.offsetWidth) * 100;
        
        let snapThreshold = 10; // Reduced threshold to make swiping more responsive
        
        // If swipe was quick, lower the threshold even more
        if (timeDiff < 300 && Math.abs(diffX) > 20) {
            snapThreshold = 3;
        }
        
        if (Math.abs(movePercent) > snapThreshold) {
            if (movePercent > 0) {
                // Swipe right, go to previous slide
                changeSlide(currentSlide - 1);
            } else {
                // Swipe left, go to next slide
                changeSlide(currentSlide + 1);
            }
        } else {
            // Return to current slide
            changeSlide(currentSlide);
        }
        
        resetAutoSlide();
        
        if (!e.touches) {
            document.removeEventListener('mousemove', handleDragMove);
            document.removeEventListener('mouseup', handleDragEnd);
        }
    }

    // Helper function to get current transform value
    function getTransformValue() {
        const style = window.getComputedStyle(sliderContainer);
        const matrix = style.getPropertyValue('transform');
        
        if (matrix === 'none' || !matrix.includes('matrix')) {
            return -currentSlide * 100 / slideCount;
        }
        
        const matrixValues = matrix.match(/matrix.*\((.+)\)/)[1].split(', ');
        const translateX = matrixValues[4];
        
        // Convert pixel translation to percentage
        return (translateX / sliderContainer.parentElement.offsetWidth) * 100;
    }

    // Start the slider by preloading images
    preloadImages();
}); 