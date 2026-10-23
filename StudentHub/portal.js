javascript
// ==========================================
// STUDENT PORTAL - DYNAMIC UI COMPONENTS
// ==========================================

document.addEventListener("DOMContentLoaded", function () {

    // ==========================================
    // 1. LIGHT / DARK THEME SWITCHER
    // ==========================================

    const themeButton = document.getElementById("themeToggleBtn");

    if (themeButton) {

        // Load saved theme
        const savedTheme = localStorage.getItem("theme");

        if (savedTheme === "dark") {
            document.documentElement.setAttribute("data-theme", "dark");
        }

        updateThemeButton();

        themeButton.addEventListener("click", function () {

            const currentTheme =
                document.documentElement.getAttribute("data-theme");

            if (currentTheme === "dark") {
                document.documentElement.setAttribute("data-theme", "light");
                localStorage.setItem("theme", "light");
            } else {
                document.documentElement.setAttribute("data-theme", "dark");
                localStorage.setItem("theme", "dark");
            }

            updateThemeButton();
        });
    }


    function updateThemeButton() {

        const icon = document.getElementById("themeIcon");
        const label = document.getElementById("themeLabel");

        const theme =
            document.documentElement.getAttribute("data-theme");

        if (theme === "dark") {

            if (icon) icon.textContent = "☀️";
            if (label) label.textContent = "Light Mode";

        } else {

            if (icon) icon.textContent = "🌙";
            if (label) label.textContent = "Dark Mode";
        }
    }



    // ==========================================
    // 2. HAMBURGER MENU
    // ==========================================

    const hamburger = document.getElementById("hamburgerBtn");
    const navigation = document.getElementById("portalNav");

    if (hamburger && navigation) {

        hamburger.addEventListener("click", function () {

            navigation.classList.toggle("show-menu");

        });
    }



    // ==========================================
    // 3. COLLAPSIBLE FAQ / ACCORDION
    // ==========================================

    const accordionHeaders =
        document.querySelectorAll(".accordion-header");

    accordionHeaders.forEach(function (header) {

        header.addEventListener("click", function () {

            const item = header.parentElement;

            // Close other accordion items
            document.querySelectorAll(".accordion-item").forEach(function (otherItem) {

                if (otherItem !== item) {
                    otherItem.classList.remove("active");
                }

            });

            // Toggle current item
            item.classList.toggle("active");

        });

    });



    // ==========================================
    // 4. MODAL POPUP
    // ==========================================

    const modalButtons =
        document.querySelectorAll("[data-modal-target]");

    modalButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            const modalId =
                button.getAttribute("data-modal-target");

            const modal =
                document.getElementById(modalId);

            if (modal) {
                modal.classList.add("active");
            }

        });

    });


    // Close modal buttons
    const closeButtons =
        document.querySelectorAll("[data-modal-close]");

    closeButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            const modal =
                button.closest(".modal-overlay");

            if (modal) {
                modal.classList.remove("active");
            }

        });

    });


    // Close modal when clicking outside
    document.querySelectorAll(".modal-overlay").forEach(function (modal) {

        modal.addEventListener("click", function (event) {

            if (event.target === modal) {
                modal.classList.remove("active");
            }

        });

    });



    // ==========================================
    // 5. NOTIFICATION BANNER
    // ==========================================

    const notification =
        document.createElement("div");

    notification.id = "notificationBanner";

    notification.innerHTML = `
        <span>
            📢 Welcome to the CHARUSAT Student Portal!
        </span>

        <button id="closeNotification">
            ✕
        </button>
    `;

    document.body.prepend(notification);

    const closeNotification =
        document.getElementById("closeNotification");

    closeNotification.addEventListener("click", function () {

        notification.style.display = "none";

    });



    // ==========================================
    // 6. CONTENT SLIDER
    // ==========================================

    const sliderContainer =
        document.createElement("div");

    sliderContainer.id = "contentSlider";

    sliderContainer.innerHTML = `

        <div class="slide active-slide">
            <h2>📚 Academic Updates</h2>
            <p>
                Check your results, attendance and registered courses
                regularly.
            </p>
        </div>

        <div class="slide">
            <h2>📅 Timetable</h2>
            <p>
                Check your latest class timetable and plan your schedule.
            </p>
        </div>

        <div class="slide">
            <h2>📢 Announcements</h2>
            <p>
                Stay updated with important college announcements.
            </p>
        </div>

        <button id="previousSlide">❮</button>

        <button id="nextSlide">❯</button>

    `;

    // Add slider after navigation
    if (navigation) {
        navigation.insertAdjacentElement(
            "afterend",
            sliderContainer
        );
    } else {
        document.body.prepend(sliderContainer);
    }


    const slides =
        document.querySelectorAll(".slide");

    let currentSlide = 0;


    function showSlide(index) {

        slides.forEach(function (slide) {
            slide.classList.remove("active-slide");
        });

        slides[index].classList.add("active-slide");

    }


    const nextButton =
        document.getElementById("nextSlide");

    const previousButton =
        document.getElementById("previousSlide");


    nextButton.addEventListener("click", function () {

        currentSlide++;

        if (currentSlide >= slides.length) {
            currentSlide = 0;
        }

        showSlide(currentSlide);

    });


    previousButton.addEventListener("click", function () {

        currentSlide--;

        if (currentSlide < 0) {
            currentSlide = slides.length - 1;
        }

        showSlide(currentSlide);

    });


    // Automatic slider
    setInterval(function () {

        currentSlide++;

        if (currentSlide >= slides.length) {
            currentSlide = 0;
        }

        showSlide(currentSlide);

    }, 4000);



    // ==========================================
    // 7. ESC KEY CLOSES MODAL
    // ==========================================

    document.addEventListener("keydown", function (event) {

        if (event.key === "Escape") {

            document.querySelectorAll(".modal-overlay").forEach(function (modal) {
                modal.classList.remove("active");
            });

        }

    });

});

// ===============================
// NOTICE DROPDOWN / ACCORDION
// ===============================

const noticeHeaders = document.querySelectorAll(".notice-header");

noticeHeaders.forEach(function(header) {

    header.addEventListener("click", function() {

        const noticeItem = header.parentElement;

        // Close other notices
        document.querySelectorAll(".notice-item").forEach(function(item) {

            if (item !== noticeItem) {
                item.classList.remove("active");
            }

        });

        // Open / close selected notice
        noticeItem.classList.toggle("active");

    });

});

