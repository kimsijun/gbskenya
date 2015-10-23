$(function(){
    $(".frmAdvertise").validate({
        rules:{
            adCompany:"required"
            ,adName:"required"
            ,adContact:"required"
            ,adEmail1:"required"
            ,adEmail2:"required"
            ,adType:"required"
            ,adContents:"required"
        }
    });

    /*
     | -------------------------------------------------------------------
     | # 메인슬라이드 Swipe 슬라이드 관련 스크립트
     | -------------------------------------------------------------------
     */
    var mySwiperMf = $('.swiper-container-mf').swiper({
        pagination: '.mfPagination',
        loop: true,
        speed:1500,
        autoplay:6000,
        autoplayDisableOnInteraction: false,
        grabCursor: true,
        paginationClickable: true
        //etc..
    });
    mySwiperMf.startAutoplay();
    $('.arrow-left-mf').on('click', function(e){
        e.preventDefault()
        mySwiperMf.swipePrev()
    })
    $('.arrow-right-mf').on('click', function(e){
        e.preventDefault()
        mySwiperMf.swipeNext()
    })
    /*
     | -------------------------------------------------------------------
     | # 굿뉴스투데이 Swipe 슬라이드 관련 스크립트
     | -------------------------------------------------------------------
     */
    var mySwiperGt = $('.swiper-container-gt').swiper({
        pagination: '.gtPagination',
        mode:'horizontal',
        loop: true,
        grabCursor: true,
        paginationClickable: true
        //etc..
    });

    /*
     | -------------------------------------------------------------------
     | # 이달의행사 Swipe 슬라이드 관련 스크립트
     | -------------------------------------------------------------------
     */
    var mySwiperMe = $('.swiper-container-me').swiper({
        pagination: '.mePagination',
        mode:'horizontal',
        loop: true,
        grabCursor: true,
        paginationClickable: true,
        autoplay:5000
        //etc..

    });
});