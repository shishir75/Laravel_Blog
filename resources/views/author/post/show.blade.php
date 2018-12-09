@extends('layouts.backend.app')

@section('title', 'Post')

@push('css')

@endpush

@section('content')
	<div class="container-fluid">
		<a href="{{ route('author.post.index') }}" class="btn btn-danger">BACK</a>
		@if($post->is_approved == false)
			<button type="button" class="btn btn-success pull-right">
				<i class="material-icons">help_outline</i>
				<span>Approve</span>
			</button>
		@else
			<button type="button" class="btn btn-success pull-right" disabled="1">
				<i class="material-icons">done</i>
				<span>Approved</span>
			</button>
		@endif
		<div class="row clearfix" style="margin-top: 10px;">
				<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
					<div class="card">
						<div class="header">
							<h2>
								{{ $post->title }}
								<small>Posted by <strong>{{ $post->user->name }}</strong> on {{
								$post->created_at->toDayDateTimeString() }}</small>
							</h2>
						</div>
						<div class="body text-justify">
							{!! $post->body !!}
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
					<div class="card">
						<div class="header bg-cyan">
							<h2>
								CATEGORIES
							</h2>
						</div>
						<div class="body">
							@foreach($post->categories as $category)
								<span class="label bg-cyan">{{ $category->name }}</span>
							@endforeach
						</div>
					</div>

					<div class="card">
						<div class="header bg-green">
							<h2>
								TAGS
							</h2>
						</div>
						<div class="body">
							@foreach($post->tags as $tag)
								<span class="label bg-green">{{ $tag->name }}</span>
							@endforeach
						</div>
					</div>

					<div class="card">
						<div class="header bg-amber">
							<h2>
								FEATURED IMAGE
							</h2>
						</div>
						<div class="body">
							<img class="img-fluid thumbnail" height="100%" width="100%" src="{{ Storage::disk('public')->url('post/'.$post->image) }}"
							alt="">
						</div>
					</div>
				</div>
			</div>


		<!-- Vertical Layout End | With Floating Label -->

	</div>
@endsection

@push('js')

@endpush