(function () {
    function updateCityDescription(select) {
        var targetId = select.dataset.descriptionTarget;
        var target = targetId ? document.getElementById(targetId) : null;
        var option = select.options[select.selectedIndex];

        if (target && option) {
            target.textContent = option.dataset.description || option.textContent;
        }
    }

    document.querySelectorAll('[data-city-select]').forEach(function (select) {
        updateCityDescription(select);
        select.addEventListener('change', function () {
            updateCityDescription(select);
        });
    });

    var swapButton = document.getElementById('change-destiny-origin');
    if (swapButton) {
        swapButton.addEventListener('click', function () {
            var origin = document.querySelector('select[name="origen"]');
            var destination = document.querySelector('select[name="destino"]');

            if (!origin || !destination) {
                return;
            }

            var originValue = origin.value;
            origin.value = destination.value;
            destination.value = originValue;
            updateCityDescription(origin);
            updateCityDescription(destination);
        });
    }

    document.querySelectorAll('[data-news-carousel]').forEach(function (carousel) {
        var slides = Array.prototype.slice.call(carousel.querySelectorAll('[data-news-slide]'));
        var currentIndex = 0;

        function showSlide(nextIndex) {
            slides.forEach(function (slide, index) {
                slide.classList.toggle('hidden', index !== nextIndex);
            });
            currentIndex = nextIndex;
        }

        carousel.querySelectorAll('.js-next-news').forEach(function (button) {
            button.addEventListener('click', function () {
                if (slides.length === 0) {
                    return;
                }

                showSlide((currentIndex + 1) % slides.length);
            });
        });
    });
})();
