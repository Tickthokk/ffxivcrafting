var quests = {
	init:function() {
		$('.class-selector').click(function() {
			$('.quest-table').addClass('hidden');
			$('#' + $(this).data('job')).removeClass('hidden');
		});

		$('.class-selector').first().trigger('click');
	}
}

$(quests.init);