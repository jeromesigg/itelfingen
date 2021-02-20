function enable_EventBtn(){
  document.getElementById("EventSubmit1").disabled = false;
  document.getElementById("EventSubmit2").disabled = false;
}

function enable_ContactBtn(){
  document.getElementById("ContactSubmit").disabled = false;
}

function adjustHeightOfPage(pageNo) {
  var pageContentHeight = 0;
  var pageType = $('div[data-page-no="' + pageNo + '"]').data("page-type");
  if( pageType != undefined && pageType == "gallery") {
      pageContentHeight = $(".cd-hero-slider li:nth-of-type(" + pageNo + ") .tm-img-gallery-container").height();
  }
  else {
      pageContentHeight = $(".cd-hero-slider li:nth-of-type(" + pageNo + ") .js-tm-page-content").height() + 20;
  }
  // Get the page height
  var totalPageHeight = $('.cd-slider-nav').height()
                          + pageContentHeight
                          + $('.tm-footer').outerHeight();
  // Adjust layout based on page height and window height
  if(totalPageHeight > $(window).height()) 
  {
      $('.cd-hero-slider').addClass('small-screen');
      $('.cd-hero-slider li:nth-of-type(' + pageNo + ')').css("min-height", totalPageHeight + "px");
  }
  else 
  {
      $('.cd-hero-slider').removeClass('small-screen');
      $('.cd-hero-slider li:nth-of-type(' + pageNo + ')').css("min-height", "100%");
  }
}