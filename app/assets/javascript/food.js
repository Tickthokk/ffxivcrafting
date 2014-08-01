var food = {
	init:function() {
		
		$('.vendors').on('click', function(event) {
			event.preventDefault();
			var el = $(this),
				id = el.data('itemId');

			if (el.hasClass('loading'))
				return;

			var modal = $('#vendors_for_' + id);

			if (modal.length == 0)
			{
				$.ajax({
					url: '/vendors/view/' + id,
					dataType: 'json',
					beforeSend:function() {

						el.addClass('loading');

						global.noty({
							type: 'warning',
							text: 'Loading Vendors'
						});
					},
					success:function(json) {
						$('body').append(json.html);

						$('#vendors_for_' + id).modal();

						el.removeClass('loading');
					}
				});
			}
			else
			{
				$('#vendors_for_' + id).modal('show');
			}

			return;
		});

		food.grid_events();

		return;
	},
	grid_events:function() {
		$('.food-grid td.reveal').click(function() {
			var el = $(this),
				a = el.data('a'),
				b = el.data('b'),
				fs = el.closest('.food-selection'),
				items = fs.find('.items-table'),
				tbody = items.find('tbody');

			items.find('caption h2').html(el.data('originalTitle'));

			var looking_for = a == b ? 1 : 2,
				bonus = 0;

			// if this cell is already active, don't do anything
			if (el.hasClass('active'))
				return;

			// Make this cell "active"
			$('.food-grid td.reveal').removeClass('active');
			el.addClass('active');

			// On pageload the items table is hidden
			items.removeClass('hidden');

			// Show all the applicable rows
			$('tr', tbody)
				.addClass('hidden')
				.each(function() {
					var el = $(this),
						stats = el.data('stats').split('|'),
						matched = 0;

					if (stats.indexOf(a) != -1)
						matched++;
					if (a != b && stats.indexOf(b) != -1)
						matched++;
					if (stats.indexOf('Vitality') != -1)
					{
						bonus = 1;
						matched++;
					}

					if (matched == looking_for + bonus && stats.length == looking_for + bonus)
						el.removeClass('hidden');

					return;
				});

			// Show/hide columns

			looking_for += bonus;

			$('.items-table th, .items-table td').removeClass('hidden');

			if (looking_for < 3)
				$('.items-table th:nth-child(4), .items-table td:nth-child(4)').addClass('hidden');
			if (looking_for < 2)
				$('.items-table th:nth-child(3), .items-table td:nth-child(3)').addClass('hidden');

			// Prepare data for the HighChart
			var series = [],
				stat_a = '',
				stat_b = '';

			$('tr:visible', tbody).each(function() {
				var el = $(this),
					name = $('td:first-child a:last-child', el).text(),
					a_td = $('td:nth-child(2)', el),
					b_td = $('td:nth-child(3)', el);

				if (b_td.length == 0)
					b_td = a_td;

				if (stat_a == '')
					stat_a = a_td.data('statName');
				if (stat_b == '')
					stat_b = b_td.data('statName');

				var quality = el.data('quality') == 'hq' ? ' (HQ)' : '';

				series.push({
					name: name + quality,
					// color: 'rgb(37, 193, 193)', // Teal
					color: 'rgb(255, 64, 64)', // Lobster
					data: [[a_td.data('amount'), b_td.data('amount')]]
				});

				return;
			});

			// Load the HighChart
			$('.highchart', fs).removeClass('hidden');
			$('.highchart', fs).highcharts({
				chart: {
					type: 'scatter',
					zoomType: 'xy'
				},
				title: {
					text: el.data('originalTitle')
				},
				// subtitle: {
				// 	text: 'Source: Heinz  2003'
				// },
				xAxis: {
					title: {
						enabled: true,
						text: stat_a
					},
					startOnTick: true,
					endOnTick: true,
					showLastLabel: true
				},
				yAxis: {
					title: {
						text: stat_b
					}
				},
				legend: {
					enabled: false
				},
				plotOptions: {
					scatter: {
						marker: {
							radius: 5,
							states: {
								hover: {
									enabled: true,
									lineColor: 'rgb(100,100,100)'
								}
							}
						},
						states: {
							hover: {
								marker: {
									enabled: false
								}
							}
						},
						tooltip: {
							headerFormat: '<b>{series.name}</b><br>',
							pointFormat: '{point.x} ' + stat_a + ', {point.y} ' + stat_b
						}
					}
				},
				series: series
			});

			return;
		});
		
		$('.items-table .sort').click(function() {
			var el = $(this),
				table = el.closest('table'),
				tbody = table.find('tbody'),
				rows = $('tr:visible', tbody);

			// Switch the Sorting icons around
			// Default them all back to "sort"
			table.find('.sort .glyphicon')
				.addClass('glyphicon-sort')
				.removeClass('glyphicon-sort-by-attributes')
				.removeClass('glyphicon-sort-by-attributes-alt');

			// Are we sorting ascending or descending?
			var order = el.data('order') == 'asc' ? 'desc' : 'asc';
			el.data('order', order); // Save the order back in

			// Change this icon
			el.find('.glyphicon')
				.removeClass('glyphicon-sort')
				.addClass('glyphicon-sort-by-attributes' + (order == 'desc' ? '-alt' : ''));

			var items = tbody.children('tr:visible').sort(function(a, b) {
				var a = parseInt($(a).find('td:nth-child(' + el.data('column') + ')').data('amount')),
					b = parseInt($(b).find('td:nth-child(' + el.data('column') + ')').data('amount'));
				return (a < b) ? -1 : (a > b) ? 1 : 0;
			});

			if (order == 'desc')
				// items is a nodelist, not an array
				items = Array.prototype.reverse.call(items);

			tbody.append(items);

			return;
		});

		return;
	},
	sort_using_data:function(parent, child, selector)
	{
		
		return;
	}
}

$(food.init);