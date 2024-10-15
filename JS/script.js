document.addEventListener('DOMContentLoaded', function () {
    const cartLink = document.querySelector('.cart a');
    let cartCount = 0;

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            cartCount++;
            cartLink.innerHTML = `Cart (${cartCount})`;
            alert('Item added to cart!');
        });
    });
});




$(document).ready(function () {
    var $slider = $('.hero-slider');
    var $sections = $('.single-slider');
    var currentSection = 0;
    
    function changeSection(index) {
        if (index < 0) index = 0;
        if (index >= $sections.length) index = $sections.length - 1;
        
        $slider.animate({ scrollTop: $sections.eq(index).offset().top }, 800);
        currentSection = index;
    }

    $(window).on('mousewheel DOMMouseScroll', function (event) {
        var delta = event.originalEvent.wheelDelta || -event.originalEvent.detail;

        if (delta > 0) {
            // Scrolling up
            changeSection(currentSection - 1);
        } else {
            // Scrolling down
            changeSection(currentSection + 1);
        }

        event.preventDefault(); 
    });

    
    $(document).on('keydown', function (event) {
        if (event.which === 38) {
            // Up arrow key
            changeSection(currentSection - 1);
        } else if (event.which === 40) {
            // Down arrow key
            changeSection(currentSection + 1);
        }
    });
});




var swiper = new Swiper('.slider-wrapper', {
    direction: 'horizontal',
    loop: true,
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});



// JS/script.js
document.addEventListener('DOMContentLoaded', function () {
    var swiper = new Swiper('.swiper-container', {
        loop: true,
        autoplay: {
            delay: 5000, 
            disableOnInteraction: false,
        },
        speed: 1000, 
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
});

$(document).ready(function(){
    // Smooth scroll to #background-container when "Branches" is clicked
    $('a[href="#background-container"]').on('click', function(event) {
      event.preventDefault(); // Prevent the default anchor behavior
      $('html, body').animate({
        scrollTop: $('#background-container').offset().top
      }, 1000); // 1000ms for smooth scroll effect
    });

    // Smooth scroll to #contact-us when "Contact Us" is clicked
    $('a[href="#contact-us"]').on('click', function(event) {
      event.preventDefault(); // Prevent the default anchor behavior
      $('html, body').animate({
        scrollTop: $('#contact-us').offset().top
      }, 1000); // 1000ms for smooth scroll effect
    });
  });



  document.querySelector('form').addEventListener('submit', function(event) {
    let name = document.getElementById('name').value.trim();
    let email = document.getElementById('email').value.trim();
    let message = document.getElementById('message').value.trim();

    if (!name || !email || !message) {
        alert("All fields are required.");
        event.preventDefault();  // Prevent form from being submitted
    }
});

