// Mobile Navigation Toggle
const hamburger = document.getElementById("hamburger");
const navLinks = document.getElementById("navLinks");

hamburger.addEventListener("click", () => {
  navLinks.classList.toggle("active");

  // Animate hamburger
  hamburger.classList.toggle("active");
});

// Close mobile menu when clicking on links
document.querySelectorAll(".nav-links a").forEach((link) => {
  link.addEventListener("click", () => {
    navLinks.classList.remove("active");
    hamburger.classList.remove("active");
  });
});

// Scroll Animation Observer
const observerOptions = {
  threshold: 0.3,
  rootMargin: "0px 0px -10px 0px",
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      setTimeout(() => {
        entry.target.classList.add("visible");
      }, 500); // 1 second delay as requested
    }
  });
}, observerOptions);

// Observe all sections except the first one (intro)
const sections = document.querySelectorAll(".section");
sections.forEach((section, index) => {
  if (index === 0) {
    // Make intro section visible immediately
    section.style.opacity = "1";
    section.style.transform = "translateY(0)";
  } else {
    observer.observe(section);
  }
});

const sections1 = document.querySelectorAll(".section1");
sections1.forEach((section1, index) => {
  if (index === 0) {
    // Make intro section visible immediately
    section1.style.opacity = "1";
    section1.style.transform = "translateY(0)";
  } else {
    observer.observe(section1);
  }
});

// Header background change on scroll
window.addEventListener("scroll", () => {
  const header = document.querySelector(".header");
  if (window.scrollY > 100) {
    header.style.background = "rgba(255, 255, 255, 0.02)";
    header.style.boxShadow = "0 2px 20px rgba(0,0,0,0.1)";
  } else {
    header.style.background = "rgba(255, 255, 255, 0.02)";
    header.style.boxShadow = "none";
  }
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  });
});

// Add some interactive effects to field items
// document.querySelectorAll(".field-item").forEach((item) => {
//   item.addEventListener("mouseenter", function () {
//     this.style.background = "linear-gradient(135deg, #4CAF50 0%, #81C784 100%)";
//     this.style.color = "white";
//   });

//   item.addEventListener("mouseleave", function () {
//     this.style.background = "linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)";
//     this.style.color = "#333";
//   });
// });

// Add typing effect to main heading
// function typeWriter(element, text, speed = 100) {
//   element.textContent = "";
//   element.style.borderRight = "2px solid white";

  let i = 0;
  function type() {
    if (i < text.length) {
      element.textContent += text.charAt(i);
      i++;
      setTimeout(type, speed);
    } else {
      element.style.borderRight = "none";
    }
  }
  type();
// }

// Start typing effect when page loads
// window.addEventListener("load", () => {
//   const mainHeading = document.querySelector(".intro-content h1");
//   const originalText = mainHeading.textContent;
//   typeWriter(mainHeading, originalText, 80);
// });










// Login page


// Typing + heartbeat for Welcome Back
document.addEventListener("DOMContentLoaded", () => {
  const textElement = document.getElementById("welcome-text");
  const text = "Welcome Back 🌱";
  let i = 0;

  // add cursor blink while typing
  textElement.classList.add("typing-active");

  function typeWriter() {
    if (i < text.length) {
      textElement.textContent += text.charAt(i);
      i++;
      setTimeout(typeWriter, 100); // typing speed
    } else {
      // stop blinking cursor and add heartbeat
      textElement.classList.remove("typing-active");
      textElement.classList.add("heartbeat");
    }
  }

  typeWriter();
});






// Register page


// Typing + heartbeat for Register page
document.addEventListener("DOMContentLoaded", () => {
  const registerElement = document.getElementById("register-text");
  if (registerElement) {
    const text = "Join Us 🌱";
    let i = 0;
    registerElement.classList.add("typing-active");

    function typeWriter() {
      if (i < text.length) {
        registerElement.textContent += text.charAt(i);
        i++;
        setTimeout(typeWriter, 100);
      } else {
        registerElement.classList.remove("typing-active");
        registerElement.classList.add("heartbeat");
      }
    }
    registerElement.textContent = ""; // clear before typing
    typeWriter();
  }
});
