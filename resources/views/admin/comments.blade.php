@extends('layouts.backend.app')

@section('title', 'Comments')

@push('css')
	<!-- JQuery DataTable Css -->
	<link href="{{ asset('assets/backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}" rel="stylesheet">
@endpush

@section('content')
	<div class="container-fluid">

		<!-- Exportable Table -->
		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					<div class="header">
						<h2>
							ALL COMMENTS
							<span class="badge bg-blue">{{ $comments->count() }}</span>
						</h2>
					</div>
					<div class="body">
						<div class="table-responsive">
							<table class="table table-bordered table-striped table-hover dataTable js-exportable">
								<thead>
									<tr class="text-center">
										<th>Comment Info</th>
										<th>Post Info</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tfoot>
									<tr class="text-center">
										<th>Comment Info</th>
										<th>Post Info</th>
										<th>Actions</th>
									</tr>
								</tfoot>
								<tbody>
									@foreach($comments as $key => $comment)
										<tr>
											<td>
												<div class="media" style="margin-bottom: 0;">
													<div class="media-left">
														<a href="#">
															<img class="media-object rounded" src="{{ Storage::disk
															('public')->url('profile/'.$comment->user->image) }}" width="64" height="64" >
														</a>
													</div>
													<div class="media-body">
														<h4 class="media-heading">
															{{ $comment->user->name }}
															<small>{{ $comment->created_at->diffForHumans() }}</small>
														</h4>
														<p>{{ str_limit($comment->comment, 180) }}</p>
														<a href="{{ route('post.details', $comment->post->slug.'#comments') }}" target="_blank">Reply</a>
													</div>
												</div>
											</td>
											<td>
												<div class="media">
													<div class="media-right">
														<a target="_blank" href="{{ route('post.details', $comment->post->slug) }}">
															<img src="{{ Storage::disk('public')->url('post/'.$comment->post->image) }}" width="64" height="64">
														</a>
													</div>
													<div class="media-body">
														<a target="_blank" href="{{ route('post.details', $comment->post->slug) }}">
															<h4 class="media-heading">{{ str_limit($comment->post->title, 40) }}</h4>
														</a>
														<p>by <strong>{{ $comment->post->user->name }}</strong></p>
													</div>
												</div>
											</td>
											<td>
												<button type="button" class="btn btn-danger waves-effect"
												onclick="deleteComment({{ $comment->id }})">
													<i class="material-icons">delete</i>
												</button>
												<form id="delete-form-{{ $comment->id }}" method="post" action="{{ route('admin.comments.destroy', $comment->id) }}" style="display: none;">
													@csrf
													@method('DELETE')
												</form>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- #END# Exportable Table -->
	</div>
@endsection

@push('js')
	<!-- Jquery DataTable Plugin Js -->
	<script src="{{ asset('assets/backend/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/buttons.flash.min.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/jszip.min.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/pdfmake.min.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/vfs_fonts.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/buttons.html5.min.js') }}"></script>
	<script src="{{ asset('assets/backend/plugins/jquery-datatable/extensions/export/buttons.print.min.js') }}"></script>

	<!-- Custom Js -->
	<script src="{{ asset('assets/backend/js/pages/tables/jquery-datatable.js') }}"></script>

	<!-- Sweet Alert Js -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.1/dist/sweetalert2.all.min.js"></script>

	<script type="text/javascript">
		function deleteComment(id) {
			const swalWithBootstrapButtons = swal.mixin({
				confirmButtonClass: 'btn btn-success',
				cancelButtonClass: 'btn btn-danger',
				buttonsStyling: false,
			})

			swalWithBootstrapButtons({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				type: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, delete it!',
				cancelButtonText: 'No, cancel!',
				reverseButtons: true
			}).then((result) => {
				if (result.value) {
					event.preventDefault();
					document.getElementById('delete-form-'+id).submit();
				} else if (
					// Read more about handling dismissals
					result.dismiss === swal.DismissReason.cancel
				) {
					swalWithBootstrapButtons(
						'Cancelled',
						'Your data is safe :)',
						'error'
					)
				}
			})
		}

	</script>


@endpush