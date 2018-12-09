@extends('layouts.frontend.app')

@section('title', 'Profile')

@push('css')
	<link href="{{ asset('assets/frontend/css/profile/styles.css') }}" rel="stylesheet">

	<link href="{{ asset('assets/frontend/css/profile/responsive.css') }}" rel="stylesheet">
	<style>
		.favorite_posts{
			color: red;
		}
	</style>
@endpush

@section('content')
	<div class="slider display-table center-text">
		<h1 class="title display-table-cell"><b>{{ $author->name }}'s Profile</b></h1>
	</div><!-- slider -->

	<section class="blog-area section">
		<div class="container">

			<div class="row">

				<div class="col-lg-8 col-md-12">
					<div class="row">
						@if($posts->count() > 0)
							@foreach($posts as $post)
							<div class="col-md-6 col-sm-12">
								<div class="card h-100">
									<div class="single-post post-style-1">

										<div class="blog-image"><img src="{{ Storage::disk('public')->url('post/'.$post->image)}}" alt="Blog Image"></div>

										<a class="avatar" href="{{ route('author.profile', $post->user->username) }}"><img src="{{ Storage::disk('public')->url('profile/'.$post->user->image)}}" alt="Profile Image"></a>

										<div class="blog-info">

											<h4 class="title"><a href="{{ route('post.details', $post->slug) }}"><b>{{ $post->title }}</b></a></h4>

											<ul class="post-footer">
												<li>
													@guest
														<a href="javascript:void(0)" onclick="toastr.info('To add Favorite ' +'list, You need to login first!', 'Info', {
														    closeButton: true,
														    progressBar: true,
														    })">
															<i class="ion-heart"></i>{{ $post->favorite_to_users->count() }}
														</a>
													@else
														<a href="javascript:void(0)" onclick="document.getElementById('favorite-form-{{ $post->id }}').submit()"
														   class="{{ !Auth::user()->favorite_posts->where('pivot.post_id',$post->id)->count() == 0 ? 'favorite_posts' : '' }}" >
															<i class="ion-heart"></i>{{ $post->favorite_to_users->count() }}
														</a>

														<form id="favorite-form-{{ $post->id }}" method="post" action="{{ route('post.favorite', $post->id) }}" style="display: none">
															@csrf
														</form>
													@endguest
												</li>
												<li><a href="#"><i class="ion-chatbubble"></i>{{ $post->comments->count()}}</a></li>
												<li><a href="#"><i class="ion-eye"></i>{{ $post->view_count }}</a></li>
											</ul>

										</div><!-- blog-info -->
									</div><!-- single-post -->
								</div><!-- card -->
							</div><!-- col-md-6 col-sm-12 -->
						@endforeach
						@else
							<div class="col-12">
								<div class="card">
									<div class="single-post" style="padding-bottom: 0px;">
										<div class="blog-info">
											<h4 class="title text-danger" style="padding-bottom: 20px;">
												<strong>Sorry! No Post Found :(</strong>
											</h4>
										</div>
									</div>
								</div>
							</div>
						@endif
					</div><!-- row -->

					{{--<a class="load-more-btn" href="#"><b>LOAD MORE</b></a>--}}

				</div><!-- col-lg-8 col-md-12 -->

				<div class="col-lg-4 col-md-12 ">

					<div class="single-post info-area ">

						<div class="about-area">
							<h4 class="title"><b>ABOUT AUTHOR</b></h4>
							<strong>Name : {{ ucwords($author->name) }}</strong><br><br>
							<p>{{ $author->about }}</p><br>
							<strong>Member Since : {{ $author->created_at->toFormattedDateString() }}</strong><br><br>
							<strong>Total Posts : {{ $author->posts()->approved()->published()->count() }}</strong><br>
						</div>

					</div><!-- info-area -->

				</div><!-- col-lg-4 col-md-12 -->

			</div><!-- row -->

		</div><!-- container -->
	</section><!-- section -->


@endsection

@push('js')

@endpush