<?php
$k =1;
foreach($booking as $key => $book):
	// Fixed 2
	if (!$countries->has($book->country_code)) continue;
	// Business URL setting
	$bizUrl = lurl(slugify($book->title) . '/' . $book->biz_id . '.html');
	//$bizUrl ="#";
	// Picture setting
	$bizImg = '';
	$pictures = \App\Larapen\Models\BusinessImage::where('biz_id', $book->biz_id);
	$countPictures = $pictures->count();
	if ($countPictures > 0) {
		if (is_file(public_path() . '/uploads/pictures/'. $pictures->first()->filename)) {
			$bizImg = url('pic/x/cache/medium/' . $pictures->first()->filename);
		}
		if ($bizImg=='') {
			if (is_file(public_path() . '/'. $pictures->first()->filename)) {
				$bizImg = url('pic/x/cache/medium/' . $pictures->first()->filename);
			}
		}
	}
	// Default picture
	if ($bizImg=='') {
		$bizImg = url('pic/x/cache/medium/' . config('larapen.laraclassified.picture'));
	}
	// Ad City
	$city = '-';
	/*if ($book->city) {
		$city = $book->city->name;
	} else {
		$city = '-';
	}*/
?>
{{--*/ $extraInfoA	= unserialize($book->extraInfo); /*--}}
{{--*/ $timeA		= explode('-', $extraInfoA['time']); /*--}}
{{--*/ $timeSt		= strtotime($timeA[0]); /*--}}
{{--*/ $timeEd		= strtotime($timeA[1]); /*--}}
{{--*/ $timeStDate	= date("h:i A", $timeSt); /*--}}
{{--*/ $timeEdDate	= date("h:i A", $timeEd); /*--}}
{{--*/ //$price		= $extraInfoA['cur']['code'].$extraInfoA['price']; /*--}}
{{--*/ $status		= t('Waiting'); /*--}}
{{--*/ $statusCls	= 'btn-oprtn-o'; /*--}}

@if($book->book_type==5) 
	{{--*/ $book_type	= t('Table'); /*--}}
	{{--*/ $sr_time		= strtotime($extraInfoA['sr_time']); /*--}}
	{{--*/ $startTime   = strtotime($book->book_date.' '.date("h:i A", $sr_time)); /*--}}
@else
	{{--*/ $book_type	= t('Timeslot'); /*--}}
	{{--*/ $startTime   = strtotime($book->book_date.' '.$timeStDate); /*--}}
@endif

@if(time()>=$startTime)
	{{--*/ $status		= t('Timeout'); /*--}}
	{{--*/ $statusCls	= 'btn-oprtn-r'; /*--}}
@endif
@if($book->approved==1) 
	{{--*/ $status	= t('Approved'); /*--}}
	{{--*/ $statusCls	= 'btn-oprtn'; /*--}}
@elseif($book->approved==2) 
	{{--*/ $status	= t('Discarded'); /*--}}
	{{--*/ $statusCls	= 'btn-oprtn-r'; /*--}}
@endif
<div class="col-sm-3">
	<div class="eo-box">
		<div class="ribbon-wrapper"><div class="ribbon-design purple-skin">{{$book_type}}</div></div>
		<div class="eo-box-title">
			<h2>				@if(strtolower(Request::segment('1')) == 'ar')					<a href="{{ $bizUrl }}" title="{{ $book->title_ar }}">{{ $book->title_ar }}</a>				@else					<a href="{{ $bizUrl }}" title="{{ $book->title }}">{{ $book->title }}</a>				@endif			</h2>
		</div>
		<div class="eo-box-content">
			@if($book->book_type==5)
				<p>{{$extraInfoA['sr_people']}} {{t('people')}}</p>
				{{--*/ $bookTimeT	= strtotime($extraInfoA['sr_time']); /*--}}
				{{--*/ $bookTime	= date("h:i A", $bookTimeT); /*--}}
				<p>{{ $bookTime }}</p>
			@else
			<p>{{ $timeStDate }} - {{ $timeEdDate  }} <small>{{($timeA[1]=='00.00')?'t("midnight next day")':''}}</small></p>
			@endif
			<p>{{date("m/d/Y", strtotime($book->book_date))}}</p>
			<a href="#" data-toggle="modal" data-target="#eo-modal{{$book->id}}" id="stDv{{$book->id}}"><button class="btn {{$statusCls}}"> {{$status}} </button></a>
		</div>
		
	</div>
</div>
<!--------------MODAL --------->
<div id="eo-modal{{$book->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">

	<!-- Modal content-->
	<div class="modal-content">
	  <div class="eo-modal-box">
	   <button type="button" class="btn eo-close" data-dismiss="modal">&times;</button>
		<div class="left-box">
			<img src="{{$bizImg}}">
		</div>
		<div class="right-box">			<h2>				@if(strtolower(Request::segment('1')) == 'ar')					<a href="{{ $bizUrl }}" title="{{ $book->title_ar }}">{{ $book->title_ar }}</a>					<br>					<small>						@if($book->biz_loc != '')							{{--*/ $unse = unserialize($book->biz_loc); /*--}}							{{ $unse->ciname }}						@else							{{ $book->ciname }}						@endif					</small>				@else					<a href="{{ $bizUrl }}" title="{{ $book->title }}">{{ $book->title }}</a>					<br>					<small>						@if($book->biz_loc != '')							{{--*/ $unse = unserialize($book->biz_loc); /*--}}							{{ $unse->ciasciiname }}						@else							{{ $book->ciasciiname }}						@endif					</small>				@endif			</h2>
			@if($book->book_type==5)
				<p>{{$extraInfoA['sr_people']}} {{t('people')}} {{t('at')}} {{date("m/d/Y h:i A", $startTime)}}</p>
			@else
			<p>{{date("m/d/Y", strtotime($book->book_date))}} {{'between'}} {{ $timeStDate }} - {{ $timeEdDate  }} <small>{{($timeA[1]=='00.00')?'t("midnight next day")':''}}</small></p>
			@endif
			<p><b>{{t('Booked by:')}}</b></p>
			<table width="100%" border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td>{{t('Name')}}</td>
					<td>{{$book->name}}</td>
				</tr>
				<tr>
					<td>{{t('Email')}}</td>
					<td>{{$book->email}}</td>
				</tr>
				<tr>
					<td>{{t('Mobile')}}</td>
					<td>{{$book->mobile}}</td>
				</tr>
				<tr>
					<td>{{t('Message')}}</td>
					<td>{{$book->notes}}</td>
				</tr>
			</table>
			<div class="eo-operation-holder" id="st-holder{{$book->id}}">
				@if($book->approved==1) 
					<span class="btn-bg-green">{{t('Approved')}}</span>
				@elseif($book->approved==2) 
					<span class="btn-bg-red">{{t('Discarded')}}</span>
				@else
					@if(time()>=$startTime)
						<span class="btn-bg-red">{{t('Timeout')}}</span>
					@else
						<a role="button" onclick="return upStatus({{$book->id}}, 1);" class="btn btneo-01">{{t('Approve')}}</a>
						<a role="button" onclick="return upStatus({{$book->id}}, 2);" class="btn btneo-01">{{t('Discard')}}</a>
					@endif
				@endif
			</div>
		</div>
	  </div>
	</div>

  </div>
</div>
<!-------END MODAL------->
<?php
endforeach;
?>
<div style="float: left; width: 100%;">{{ $booking->links() }}</div>