  // Porfolio isotope and filter
  $(window).on('load', function() {
    var portfolioIsotope = $('.portfolio-container').isotope({
      itemSelector: '.portfolio-item',
      layoutMode: 'fitRows'
    });
    $('#portfolio-flters li').on('click', function() {
      $("#portfolio-flters li").removeClass('filter-active');
      $(this).addClass('filter-active');
      portfolioIsotope.isotope({
        filter: $(this).data('filter')
      });
      aos_init();
    });
  });

  function isFutureDate(idate){
    var today = new Date().getTime(),
    idate = idate.split("-");
    idate = new Date(idate[0], idate[1] - 1, idate[2]).getTime();
    return (today - idate) < 0;
  };

  function getDate(date, delta) {
    var date_string = moment(date);
    return date_string.add(delta, 'days').format("YYYY-MM-DD");
  };

  function isOverlapping(start, end, array){
    for(i in array){  
      var db_end = getDate(array[i].end, -1);
      if (moment(end) >= moment(array[i].start) && moment(start) <= moment(db_end)){
          return true;
      }
    }
    return false;
  } 

  function enable_EventBtn(){
    document.getElementById("EventSubmit1").disabled = false;
    document.getElementById("EventSubmit2").disabled = false;
  }

  function enable_ContactBtn(){
    document.getElementById("ContactSubmit").disabled = false;
  }