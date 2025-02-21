jQuery(document).ready(function($) {

    $('.aib-board section').on('click', function(){
      var $this = $(this);
  
      // Already showing the description?
      if ($this.hasClass('aib-show-desc')) {
        // Hide the description
        $this.find('.aib-cover').css({ top: '0' });
        $this.find('.aib-desc').css({ top: '100%' });
  
        // Animate text back in
        $this.find('h5')
          .removeClass('scoot-out')
          .addClass('scoot-in');
  
        $this.removeClass('aib-show-desc');
      } 
      else {
        // Show the description
        $this.find('.aib-cover').css({ top: '-100%' });
        $this.find('.aib-desc').css({ top: '0' });
  
        // Animate text out
        $this.find('h5')
          .removeClass('scoot-in')
          .addClass('scoot-out');
  
        $this.addClass('aib-show-desc');
      }
    });
  
  });
  