// Initialize Pharmacy Swiper
var swiper = new Swiper('.pharmacy-swiper-container', {
    slidesPerView: 1,
    spaceBetween: 30,
    navigation: {
        nextEl: '.pharmacy-swiper-button-next',
        prevEl: '.pharmacy-swiper-button-prev',
    },
    pagination: {
        el: '.pharmacy-swiper-pagination',
        clickable: true,
    },
    breakpoints: {
        640: {
            slidesPerView: 1,
            spaceBetween: 20,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 30,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 40,
        },
    },
});
