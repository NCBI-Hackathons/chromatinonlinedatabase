function displayAll() {
	$('btn#btn-display-all').on('click',function() {
		$('select#select-genes option[value="display"]').prop('selected', true);
		$('select#select-genes option[value="display"]').attr('selected', true);
		$('select#select-fires option[value="display"]').prop('selected', true);
        $('select#select-fires option[value="display"]').attr('selected', true);

                              $('select#select-tads option[value="display"]').prop('selected', true);
                              $('select#select-tads option[value="display"]').attr('selected', true);

                              $('select#select-se option[value="display"]').prop('selected', true);
                              $('select#select-se option[value="display"]').attr('selected', true);

                              $('select#select-ctcf option[value="display"]').prop('selected', true);
                              $('select#select-ctcf option[value="display"]').attr('selected', true);

                              $('select#select-chip option[value="display"]').prop('selected', true);
                              $('select#select-chip option[value="display"]').attr('selected', true);

                              $('select#select-snps option[value="display"]').prop('selected', true);
                              $('select#select-snps option[value="display"]').attr('selected', true);

                              $('select#select-py option[value="display"]').prop('selected', true);
                              $('select#select-py option[value="display"]').attr('selected', true);

	});
}

function hideAll() {
	$('btn#btn-display-all').on('click',function() {
		$('select#select-genes option[value="hide"]').prop('selected', true);
		$('select#select-genes option[value="hide"]').attr('selected', true);
		$('select#select-fires option[value="hide"]').prop('selected', true);
                              $('select#select-fires option[value="hide"]').attr('selected', true);

                              $('select#select-tads option[value="hide"]').prop('selected', true);
                              $('select#select-tads option[value="hide"]').attr('selected', true);

                              $('select#select-se option[value="hide"]').prop('selected', true);
                              $('select#select-se option[value="hide"]').attr('selected', true);

                              $('select#select-ctcf option[value="hide"]').prop('selected', true);
                              $('select#select-ctcf option[value="hide"]').attr('selected', true);

                              $('select#select-chip option[value="hide"]').prop('selected', true);
                              $('select#select-chip option[value="hide"]').attr('selected', true);

                              $('select#select-snps option[value="hide"]').prop('selected', true);
                              $('select#select-snps option[value="hide"]').attr('selected', true);

                              $('select#select-py option[value="hide"]').prop('selected', true);
                              $('select#select-py option[value="hide"]').attr('selected', true);

	});
}