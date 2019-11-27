(function($) {
    $(function() {
	/* ------------------- START VARIATION BUTTONS -------------------------- */
    /*
	$('.variations_buttons').remove();
	$('<div class="variations_buttons">').insertBefore($('.variations').closest('form'));

	$('.variations tr').each(function() {
		var $c = $('<div>').appendTo('.variations_buttons');
		$(this).find('label').clone().appendTo($c);
		var $d = $('<div class="attribute_buttons">').appendTo($c);
		$(this).find('select').each(function() {
			var $s = $(this);
			$(this).find('option').each(function() {;
				var $opt =  $(this);
				if ($opt.attr('value') == "") return;

				$d.append('<button class="button '+($opt.attr('selected') ? 'active' : '')+'" type="button" data-field="'+$s.attr('name')+'" data-value="'+$opt.attr('value')+'">'+$opt.text()+'</button>');
			});
		});
	});
	$('.variations').hide();
	$('.variations_buttons').on('click', 'button', function(event) {
		var fld = $(this).attr('data-field');
		var val = $(this).attr('data-value');
		$('select[name='+fld+'] option').attr('selected', null);
		$('select[name='+fld+'] option[value='+val+']').attr('selected', 'selected');
		$('select[name='+fld+']').trigger('change');

		$('form.cart .qty').trigger('change');


	});

	$('.variations_buttons .button').click( function() {
		$(this).closest(".attribute_buttons").find('.button').removeClass('active');
		$(this).addClass('active');
	});
    */
	/* ------------------- END VARIATION BUTTONS -------------------------- */


	setTimeout(function() {
   		var cntxt = $('.dgwt-wcas-search-input:eq(0)').closest('[data-wcas-context]').attr('data-wcas-context');
		$("[data-wcas-context]").each(function() { $(this).attr("data-wcas-context", cntxt); $(this).data('wcas-context', cntxt); });
	}, 600);



    })
})(jQuery);
