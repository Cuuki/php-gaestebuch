$(document).ready(function ()
{
    $('[data-toggle="offcanvas"]').click(function ()
    {
        $('.row-offcanvas').toggleClass('active');
    
        if( $('.row-offcanvas').hasClass('active') )
        {        
            $('.navbar.navbar-inverse').css( 'width', '175%' );
        }
        else
        {
            $('.navbar.navbar-inverse').removeAttr( 'style' );
        }
    });
});