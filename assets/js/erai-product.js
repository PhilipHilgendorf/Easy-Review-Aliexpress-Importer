(function($) {
    $('.photoswipe-gallery').on('click', 'a', function(event) {
        event.preventDefault();

        var pswpElement = document.querySelectorAll('.pswp')[0];
        var items = [];

        $('.photoswipe-gallery a').each(function() {
            var size = $(this).data('size').split('x');
            items.push({
                src: $(this).attr('href'),
                w: parseInt(size[0], 10),
                h: parseInt(size[1], 10),
                title: $(this).next('figcaption').html()
            });
        });

        var options = {
            index: $(this).parent().index(),
            bgOpacity: 0.7,
            showHideOpacity: true
        };

        var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
        gallery.init();
    });
})(jQuery);
