jQuery(document).ready(function($){
    // Icon Selector Logic
    $('.side-cart-option').click(function(){ 
        // 1. Visual: Update the 'selected' class
        $('.side-cart-option').removeClass('selected'); 
        $(this).addClass('selected'); 
    });
});