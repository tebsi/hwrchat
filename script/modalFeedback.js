/* Feedback-Modal - begin */
$(document).ready(function() {
    var MAX_missingS = 10; // wie viele Inputs?
    $('#formFeedback')
        .on('click', '.addButton', function() {
            var $template = $('#missingTemplate'),
                $clone    = $template
                                .clone()
                                .removeClass('hide')
                                .removeAttr('id')
                                .insertBefore($template),
                $missing   = $clone.find('[name="missing[]"]');
                if ($('#formFeedback').find(':visible[name="missing[]"]').length >= MAX_missingS) {
                    $('#formFeedback').find('.addButton').attr('disabled', 'disabled');
				}
        })
        .on('click', '.removeButton', function() {
            var $row    = $(this).parents('.form-group'),
                $missing = $row.find('[name="missing[]"]');
            $row.remove();
			if ($('#formFeedback').find(':visible[name="missing[]"]').length < MAX_missingS) {
				$('#formFeedback').find('.addButton').removeAttr('disabled');
			}
        });
});

function saveFeedback() // für post-Form
{
	var getdata = $('#formFeedback').serialize();
	$.post("saveFeedback.php", getdata)
		.done(function(data)
		{
			//alert("Erfolgreich übermittelt");
			$('#modalFeedbackSuccess').modal('show');
			$('#modalFeedback').modal('hide');
		})
		.fail(function(data)
		{
			//alert("Fehler beim übermitteln");
			$('#modalFeedbackFail').modal('show');
		});
	return false;
}

function closeModalFeedback(nameFromModalWhichHasToClose)
{
	$('#' + nameFromModalWhichHasToClose).modal('hide');
	return false;
}
/* Feedback-Modal - end */