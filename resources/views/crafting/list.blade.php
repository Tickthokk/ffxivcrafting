@extends('app')

@section('meta')
	<meta name="robots" content="noindex,nofollow">
@stop

@section('vendor-css')
	<link href='{{ cdn('/css/bootstrap-switch.css') }}' rel='stylesheet'>
	<link href='{{ cdn('/css/bootstrap-tour.min.css') }}' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/crafting.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-tour.min.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-switch.js') }}'></script>
@stop

@section('banner')

	<a href='#' id='start_tour' class='start btn btn-primary pull-right hidden-print' style='margin-top: 12px;'>
		<i class='glyphicon glyphicon-play'></i>
		Tour
	</a>

	<a href='#' id='csv_download' class='btn btn-info pull-right hidden-print' style='margin-top: 12px; margin-right: 10px;'>
		<i class='glyphicon glyphicon-download-alt'></i>
		Download
	</a>

	{{--
	<span class="dropdown pull-right hidden-print" style='margin-top: 12px; margin-right: 10px;'>
		<button class='btn btn-success' id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class='glyphicon glyphicon-globe'></i>
			Map
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
			<li><a href='#' id='map_all'>All</a></li>
			<li><a href='#' id='map_remaining'>Remaining</a></li>
		</ul>
	</span>
	--}}

	<h1 class='csv-filename' style='margin-top: 0;'>
		@if(isset($job))
		@if(count(explode(',', $desired_job)) == 1)
		<i class='class-icon {{ $desired_job }} large hidden-print' style='position: relative; top: 5px;'></i>
		{{ $full_name_desired_job }}
		@else
		Crafting for {{ implode(', ', explode(',', $desired_job)) }} 
		@endif
		@else
		Custom Recipe List
		@endif
	</h1>
	@if(isset($job))
	<h2>
	@if($start <= config('site.max_level'))
	recipes between ilevels {{ $start }} and {{ $end }}
	@else
		@if ($start == 55)
			<i class='glyphicon glyphicon-star'></i>
		@elseif ($start == 70)
			<i class='glyphicon glyphicon-star'></i>
			<i class='glyphicon glyphicon-star'></i>
		@elseif ($start == 90)
			<i class='glyphicon glyphicon-star'></i>
			<i class='glyphicon glyphicon-star'></i>
			<i class='glyphicon glyphicon-star'></i>
		@elseif ($start == 110)
			<i class='glyphicon glyphicon-star'></i>
			<i class='glyphicon glyphicon-star'></i>
			<i class='glyphicon glyphicon-star'></i>
			<i class='glyphicon glyphicon-star'></i>
		@endif
		recipes
	@endif
	</h2>
	@endif
@stop

@section('content')


<div class='table-responsive'>
	<table class='table table-bordered table-striped text-center' id='obtain-these-items'>
		<thead>
			<tr>
				<th class='text-center'>Item</th>
				<th class='text-center' width='75'>Needed</th>
				<th class='text-center hidden-print' width='102'>Obtained</th>
				<th class='text-center hidden-print' width='75'>Total</th>
				<th class='text-center' width='100'>Purchase</th>
				<th class='text-center'>Source</th>
			</tr>
		</thead>
		@foreach($reagent_list as $section => $list)
		<?php if (empty($list)) continue; ?>
		<?php if (isset($list[1]) && empty($list[1])) continue; ?>
		<tbody id='{{ preg_replace('/\s|\-/', '', $section) }}-section'>
			<tr>
				<th colspan='6'>
					<button class='btn btn-default pull-right glyphicon glyphicon-chevron-down collapse'></button>
					<div style='margin-top: 4px;'>Origin: {{ $section }}</div>
				</th>
			</tr>
			@foreach($list as $level => $reagents)
			<?php $i = 0; ?>
			@foreach($reagents as $reagent)
			<?php $item =& $reagent['item']; ?>
			<?php
				$requires = []; $yields = 1;
				$item_level = $item->level;
				$link = 'item/' . $item->id;
				if ($section == 'Pre-Requisite Crafting')
				{
					$item_level = $item->recipes[0]->level;
					$yields = $item->recipes[0]->yield;
					foreach ($item->recipes[0]->reagents as $rr_item)
						$requires[] = $rr_item->pivot->amount . 'x' . $rr_item->id;
					// $link = 'recipe/' . $item->recipes[0]->id;
				}
			?>
			<tr class='reagent' data-item-id='{{ $item->id }}' data-requires='{{ implode('&', $requires) }}' data-yields='{{ $yields }}'>
				<td class='text-left'>
					@if($level != 0)
					<a class='close ilvl' rel='tooltip' title='Level'>
						{{ $item_level }}
					</a>
					@endif
					<a href='http://xivdb.com/?{{ $link }}' target='_blank'>
						<img src='{{ assetcdn('item/' . $item->icon . '.png') }}' width='36' height='36' class='margin-right'><span class='name'>{{ $item->name }}</span>
					</a>
					@if ($yields > 1)
					<span class='label label-primary' rel='tooltip' title='Amount Yielded' data-container='body'>
						x {{ $yields }}
					</span>
					@endif
				</td>
				<td class='needed valign hidden-print'>
					<span>...<!--{{ $reagent['make_this_many'] }}--></span>@if(isset($reagent['both_list_warning']))
					<a href='#' class='nowhere tt-force' rel='tooltip' title='Note: Item exists in main list but is also required for another.'>*</a>
					@endif
				</td>
				<td class='valign hidden-print'>
					<div class='input-group'>
						<input type='number' class='form-control obtained text-center' min='0' value='0' step='{{ $yields }}' style='padding: 6px 3px;'>
						<div class='input-group-btn'>
							<button class='btn btn-default obtained-ok' type='button' style='padding: 6px 6px;'><span class='glyphicon glyphicon-ok-circle'></span></button>
						</div>
					</div>
				</td>
				<td class='valign total'>0</td>
				<td>
					@if(count($item->shops))
					<a href='#' class='btn btn-default click-to-view{{ $reagent['self_sufficient'] ? ' opaque' : '' }}' data-type='shops' rel='tooltip' title='Available for {{ $item->price }} gil, Click to load Vendors'>
						<img src='/img/coin.png' width='24' height='24'>
						{{ number_format($item->price) }}
					</a>
					@endif
				</td>
				<td class='crafted_gathered'>
					@foreach(array_keys(array_reverse($reagent['cluster_jobs'])) as $cluster_job)
					<i class='class-icon click-to-view {{ $cluster_job }} clusters' data-type='{{ strtolower($cluster_job) }}nodes' title='{{ $cluster_job }}'></i>
					@endforeach
					@foreach($item->recipes as $recipe)
					<i class='class-icon click-to-view {{ $recipe->job->abbr }}' data-type='recipes' title='{{ $recipe->job->abbr }}'></i>
					@endforeach
					@if(count($item->mobs))
					<img src='/img/mob.png' class='click-to-view mob-icon' data-type='mobs' width='20' height='20' rel='tooltip' title='Click to load Beasts' data-container='body'>
					@endif
				</td>
				<?php continue; ?>
			</tr>
			@endforeach
			@endforeach
		</tbody>
		@endforeach
		<tbody id='CraftingList-section'>
			<tr>
				<th colspan='6'>
					<button class='btn btn-default pull-right glyphicon glyphicon-chevron-down collapse'></button>
					<div style='margin-top: 4px;'>Crafting List</div>
				</th>
			</tr>
			@foreach($recipes as $recipe)
			<?php
				$requires = [];
				foreach ($recipe->reagents as $item)
					$requires[] = $item->pivot->amount . 'x' . $item->id;
			?>
			<tr class='reagent exempt' data-item-id='{{ $recipe->item->id }}' data-requires='{{ implode('&', $requires) }}' data-yields='{{ $recipe->yield }}'>
				<td class='text-left'>
					<a class='close ilvl' rel='tooltip' title='Level'>
						{{ $recipe->recipe_level }}
					</a>
					{{-- <a href='http://xivdb.com/?recipe/{{ $recipe->id }}' target='_blank'> --}}
					<a href='http://xivdb.com/?item/{{ $recipe->item->id }}' target='_blank'>
						<img src='{{ assetcdn('item/' . $recipe->item->icon . '.png') }}' width='36' height='36' style='margin-right: 5px;'><span class='name'>{{ $recipe->item->name }}</span>
					</a>
					@if ($recipe->req_craftsmanship)
					<span class='craftsmanship pull-right margin-right' rel='tooltip' title='Required Craftsmanship'>
						<img src="/img/stats/nq/Craftsmanship.png" class="stat-icon">
						{{ $recipe->req_craftsmanship }}
					</span>
					@endif
					@if ($recipe->req_control)
					<span class='control pull-right margin-right' rel='tooltip' title='Required Control'>
						<img src="/img/stats/nq/Control.png" class="stat-icon">
						{{ $recipe->req_control }}
					</span>
					@endif
					@if ($recipe->yield > 1)
					<span class='label label-primary' rel='tooltip' title='Amount Yielded' data-container='body'>
						x {{ $recipe->yield }}
					</span>
					@endif
					<div class='pull-right' style='clear: right;'>
						@if($include_quests && isset($recipe->item->quest[0]))
						<img src='/img/{{ $recipe->item->quest[0]->quality ? 'H' : 'N' }}Q.png' rel='tooltip' title='Turn in {{ $recipe->item->quest[0]->amount }}{{ $recipe->item->quest[0]->quality ? ' (HQ)' : '' }} to the Guildmaster{{ $recipe->item->quest[0]->notes ? ', see bottom for note' : '' }}' width='24' height='24'>
						@endif

						@if(count($recipe->item->leve_required))
							@foreach ($recipe->item->leve_required as $leve)
							@if($leve->repeats)
							<img src='/img/leve_icon_red.png' rel='tooltip' title='{{ $leve->name }}. Repeatable Leve!' style='margin-left: 5px;' width='16'>
							@else
							<img src='/img/leve_icon.png' rel='tooltip' title='{{ $leve->name }}' style='margin-left: 5px;' width='16'>
							@endif
							@endforeach
						@endif
					</div>
				</td>
				<td class='needed valign hidden-print'>
					<?php 
						$needed = (isset($item_amounts) && isset($item_amounts[$recipe->item->id]) ? $item_amounts[$recipe->item->id] : (1 + (@$recipe->item->quest[0]->amount ? $recipe->item->quest[0]->amount - 1 : 0))); 
						$needed = ceil($needed / $recipe->yield) * $recipe->yield;
					?>

					<input type='number' class='recipe-amount form-control text-center' min='0' step='{{ $recipe->yield }}' value='{{ $needed }}' style='padding: 6px 3px;'>
				</td>
				<td class='valign hidden-print'>
					<div class='input-group'>
						<input type='number' class='form-control obtained text-center' min='0' step='{{ $recipe->yield }}' value='0' style='padding: 6px 3px;'>
						<div class='input-group-btn'>
							<button class='btn btn-default obtained-ok' type='button' style='padding: 6px 6px;'><span class='glyphicon glyphicon-ok-circle'></span></button>
						</div>
					</div>
				</td>
				<td class='valign total'>{{ $needed }}</td>
				<td>
					@if(count($recipe->item->shops))
					<a href='#' class='btn btn-default click-to-view{{ $reagent['self_sufficient'] ? ' opaque' : '' }}' data-type='shops'>
						<img src='/img/coin.png' width='24' height='24' rel='tooltip' title='Available for {{ $recipe->item->price }} gil, Click to load Vendors'>
						{{ number_format($recipe->item->price) }}
					</a>
					@endif
				</td>
				<td class='crafted_gathered'>
					@if (is_null($recipe->job))
					<img src='/img/FC.png' width='20' height='20' class='click-to-view' data-type='recipes' title='Free Company Craft'></i>
					@else
					<i class='class-icon {{ $recipe->job->abbr }} click-to-view' data-type='recipes' title='{{ $recipe->job->abbr }}'></i>
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

<a name='options'></a>
@if( ! isset($item_special))
<div class='panel panel-info hidden-print'>
	<div class='panel-heading'>
		<small class='pull-right'><em>* Refreshes page</em></small>
		<h3 class='panel-title'>Options</h3>
	</div>
	<div class='panel-body'>
		@if( ! isset($item_ids))
		{!! Form::open(['url' => '/crafting', 'class' => 'form-horizontal', 'id' => 'self-sufficient-form']) !!}
			<input type='hidden' name='class' value='{{ $desired_job }}'>
			<input type='hidden' name='start' value='{{ $start }}'>
			<input type='hidden' name='end' value='{{ $end }}'>
			<label>
				Self Sufficient
			</label>
			
				<input type='checkbox' id='self_sufficient_switch' name='self_sufficient' value='1'{{ $self_sufficient ? " checked='checked'" : '' }} class='bootswitch' data-on-text='YES' data-off-text='NO'>
			
			<label class='margin-left'>
				Dyes &amp; Furniture
			</label>
			
				<input type='checkbox' id='misc_items_switch' name='misc_items' value='1' {{ $misc_items ? " checked='checked'" : '' }} class='bootswitch' data-on-text='YES' data-off-text='NO'>
			
			<label class='margin-left'>
				Component Items
			</label>
			
				<input type='checkbox' id='component_items_switch' name='component_items' value='1' {{ $component_items ? " checked='checked'" : '' }} class='bootswitch' data-on-text='YES' data-off-text='NO'>
			
		{!! Form::close() !!}
		@else
		{!! Form::open(['url' => '/crafting/list', 'class' => 'form-horizontal', 'id' => 'self-sufficient-form']) !!}
			<input type='hidden' name='List:::{{ $self_sufficient ? 0 : 1 }}' value=''>
			<label>
				Self Sufficient
			</label>
			
				<input type='checkbox' id='self_sufficient_switch' value='1'{{ $self_sufficient ? " checked='checked'" : '' }} class='bootswitch' data-on-text='YES' data-off-text='NO'
			
			<small><em>* Refreshes page</em></small>
		{!! Form::close() !!}
		@endif
	</div>
</div>
@endif

<div class='row '>
	@if(isset($job))
	<div class='col-sm-6'>
		@if($end - $start >= 4)
		<div class='panel panel-primary' id='leveling-information'>
			<div class='panel-heading'>
				<h3 class='panel-title'>Leveling Information</h3>
			</div>
			<div class='panel-body'>
				<p>Be efficient, make quest items in advance!</p>
				<p>Materials needed already reflected in lists above.</p>

				<ul>
					@foreach($quest_items as $quest)
					<li>
						@if(count($job) > 2)
						{{ $quest->job->abbr }} 
						@endif
						Level {{ $quest->level }}: 
						@if ( ! $quest->requirements)
							No data!
						@else
							@foreach ($quest->requirements as $req_item)
							{{ $req_item->name }}
							@endforeach
						@endif
					</li>
					@endforeach
				</ul>

				<p><em>Want to level faster?  Visit the <a href='/leve'>Leves</a> page.</em></p>
			</div>
		</div>
		@endif
	</div>
	@endif
	<div class='hidden-print col-sm-{{ isset($job) ? '6' : '12' }}'>
		<div class='panel panel-info'>
			<div class='panel-heading'>
				<h3 class='panel-title'>Tips</h3>
			</div>
			<div class='panel-body text-center'>
				<p>Get extras in case of a failed synthesis.</p>

				<p>Improve your chances for HQ items by using the <a href='/gear'>gear profiler</a>.</p>

				<p>Don't forget the <a href='/food'>food</a> or <a href='/materia'>materia</a>!</p>
			</div>
		</div>
	</div>
</div>

@stop