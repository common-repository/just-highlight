jQuery(document).ready(function($) {
    var windowHeight = $(window).height(), gridTop = windowHeight * .3, gridBottom = windowHeight * .6;
    var colorSelect = optionsValues.colorSelect;
    var animationActive = optionsValues.animationActive;
    var animationScroll = optionsValues.animationScroll;

    function sigijh_set_animation($this) {
        var animationSpeed = optionsValues.animationSpeed;
        var speedNum = 0.3;
        switch (animationSpeed) {
            case 'Slow':
                speedNum = 0.6;
                break;
            case 'Fast':
                speedNum = 0.15;
                break;
            case 'Very fast':
                speedNum = 0.075;
                break;
            default:
                speedNum = 0.3;
                break;
        }
        var words = $this.html().split(" ").length;
        var duration = words * speedNum;
        $this.css("-webkit-animation-duration", duration + 's');
        $this.css("background-image","linear-gradient(to right,rgba(0,0,0,0) 50%,"+colorSelect+" 50%)");
        $this.addClass("sigijh_animateMe");
    }

    if(animationActive == 'No'){
        $('.sigijh_hlt').each(function() {
            $(this).css("background-color",colorSelect);
        });
    }

    if(animationScroll == 'No'){
        $('.sigijh_hlt').each(function() {
            sigijh_set_animation($(this));
            
        });
    }
    
    $(window).on('scroll', function() {
        $('.sigijh_hlt').each(function() {
            var thisTop = $(this).offset().top - $(window).scrollTop(); 
            if (thisTop >= gridTop && (thisTop + $(this).height()) <= gridBottom) {
                if(animationActive != 'No' && animationScroll != 'No'){
                    sigijh_set_animation($(this));
                }
            } 
        });
    });
});
