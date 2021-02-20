<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
 
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
 
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="Schliessen (Esc)"></button>
                <button class="pswp__button pswp__button--share" title="Share"></button>
                <button class="pswp__button pswp__button--fs" title="Vollbild"></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                      <div class="pswp__preloader__cut">
                        <div class="pswp__preloader__donut"></div>
                      </div>
                    </div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div> 
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="Vorheriges (Pfeil links)">
            </button>
            <button class="pswp__button pswp__button--arrow--right" title="Nächstes (Pfeil Rechts)">
            </button>
            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>
        </div>
    </div>
</div>
<script>
    var $pswp = $('.pswp')[0];
    var image = [];

    $('.gallery').each( function() {
        var $pic = $(this),
            getItems = function() {
                var items = [];
                $pic.find('a').each(function() {
                    var $href   = $(this).attr('href');

                    var item = {
                        src : $href,
                        w   : 0,
                        h   : 0
                    }

                    items.push(item);
                });
                return items;
            }

        var items = getItems();

        $.each(items, function(index, value) {
            image[index] = new Image();
            image[index].src = value['src'];
        });
        $pic.on('click', 'figure', function(event) {
            event.preventDefault();
            var $index = $(this).parent().index();
            console.log($(this).parent());
            var options = {
                index: $index,
                bgOpacity: 0.7,
                showHideOpacity: true
            }

            var lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
            lightBox.listen('gettingData', function(index, item) {
                    if (item.w < 1 || item.h < 1) { // unknown size
                    var img = new Image(); 
                    img.onload = function() { // will get size after load
                    item.w = this.width; // set image width
                    item.h = this.height; // set image height
                    lightBox.invalidateCurrItems(); // reinit Items
                    lightBox.updateSize(true); // reinit Items
                    }
                img.src = item.src; // let's download image
                }
            });
            lightBox.init();
        });
    });
</script>

