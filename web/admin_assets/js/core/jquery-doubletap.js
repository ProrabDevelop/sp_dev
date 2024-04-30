(function($){ //based on https://gist.github.com/1016011
    var hasTouch = /android|iphone|ipad/i.test(navigator.userAgent.toLowerCase()),
        eventName = hasTouch ? 'touchstart' : 'click';

    /**
     * Bind an event handler to the "double tap" JavaScript event.
     * @param {function} doubleTapHandler
     * @param {number} [delay=300]
     */
    $.fn.doubletap = function(doubleTapHandler, delay){

        if(doubleTapHandler !== null && typeof doubleTapHandler === 'function'){
            this.each(function(){
                var delay = (delay == null) ? 400 : delay;
                var lastTouch = new Date().getTime() - delay - 1;
                $(this).bind(eventName, function(event){
                    var now = new Date().getTime();
                    var delta = now - lastTouch;
                    if(delta < delay){
                        // After we detct a doubletap, start over
                        lastTouch = now - delay - 1;
                        return doubleTapHandler.call($(this), event);
                    }else{
                        lastTouch = now;
                    }
                });
            });
        }else{
            console.warn('doubleTapHandler argument is not a function');
        }
    };
    
})(jQuery);