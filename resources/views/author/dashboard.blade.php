@extends('layouts.backend.app')

@push('css')

@endpush

@section('title', 'Author')

@section('content')
	<div class="container-fluid">
		<div class="block-header">
			<h2>DASHBOARD</h2>
		</div>

		<!-- Widgets -->
		<div class="row clearfix">
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<div class="info-box bg-pink hover-expand-effect">
					<div class="icon">
						<i class="material-icons">playlist_add_check</i>
					</div>
					<div class="content">
						<div class="text">TOTAL POSTS</div>
						<div class="number count-to" data-from="0" data-to="{{ $posts->count() }}" data-speed="1000" data-fresh-interval="20"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<div class="info-box bg-cyan hover-expand-effect">
					<div class="icon">
						<i class="material-icons">favorite</i>
					</div>
					<div class="content">
						<div class="text">FAVORITE POSTS</div>
						<div class="number count-to" data-from="0" data-to="{{ Auth::user()->favorite_posts()->count()}}" data-speed="1000" data-fresh-interval="20"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<div class="info-box bg-light-green hover-expand-effect">
					<div class="icon">
						<i class="material-icons">library_books</i>
					</div>
					<div class="content">
						<div class="text">PENDING POSTS</div>
						<div class="number count-to" data-from="0" data-to="{{ $total_pending_posts }}" data-speed="1000" data-fresh-interval="20"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<div class="info-box bg-orange hover-expand-effect">
					<div class="icon">
						<i class="material-icons">person_add</i>
					</div>
					<div class="content">
						<div class="text">TOTAL VIEWS</div>
						<div class="number count-to" data-from="0" data-to="{{ $all_views }}" data-speed="1000"
						data-fresh-interval="20"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- #END# Widgets -->


		<div class="row clearfix">
			<!-- Task Info -->
			<div class="col-12">
				<div class="card">
					<div class="header">
						<h2>TOP 5 POPULAR POSTS</h2>
					</div>
					<div class="body">
						<div class="table-responsive">
							<table class="table table-hover dashboard-task-infos">
								<thead>
								<tr>
									<th>Rank List</th>
									<th>Title</th>
									<th>Views</th>
									<th>Favorite</th>
									<th>Comments</th>
									<th>Status</th>
								</tr>
								</thead>
								<tbody>
									@foreach($popular_posts as $key => $post)
										<tr>
											<td>{{ $key + 1 }}</td>
											<td><a href="{{ route('post.details', $post->slug) }}">{{ str_limit($post->title, 30)}}</a></td>
											<td>{{ $post->view_count }}</td>
											<td>{{ $post->favorite_to_users_count }}</td>
											<td>{{ $post->comments_count }}</td>
											<td>
												@if($post->status == true)
													<span class="label bg-green">Published</span>
												@else
													<span class="label bg-red">Pending</span>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<!-- #END# Task Info -->
		</div>
	</div>
@endsection

@push('js')
	<!-- Jquery CountTo Plugin Js -->
	<script src="{{ asset('assets/backend/plugins/jquery-countto/jquery.countTo.js') }}"></script>

	<!-- Morris Plugin Js -->
	<script src="{{ asset('assets/backend/plugins/raphael/raphael.min.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/morrisjs/morris.js') }}"></script>

	<!-- ChartJs -->
	<script src="{{ asset('assets/backend/plugins/chartjs/Chart.bundle.js') }}"></script>

	<!-- Flot Charts Plugin Js -->
	<script src="{{ asset('assets/backend/plugins/flot-charts/jquery.flot.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/flot-charts/jquery.flot.resize.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/flot-charts/jquery.flot.pie.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/flot-charts/jquery.flot.categories.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/flot-charts/jquery.flot.time.js') }}"></script>

	<!-- Sparkline Chart Plugin Js -->
	<script src="{{ asset('assets/backend/plugins/jquery-sparkline/jquery.sparkline.js') }}"></script>

	<!-- Custom Script -->
	<script src="{{ asset('assets/backend/js/pages/index.js') }}"></script>
@endpush
