@extends('layouts.backend.app')

@section('title', 'Authors')

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
							ALL Category
							<span class="badge bg-blue">{{ $authors->count() }}</span>
						</h2>
					</div>
					<div class="body">
						<div class="table-responsive">
							<table class="table table-bordered table-striped table-hover dataTable js-exportable">
								<thead>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Posts</th>
									<th>Comments</th>
									<th>Favorite Posts</th>
									<th>Created At</th>
									<th>Actions</th>
								</tr>
								</thead>
								<tfoot>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Posts</th>
									<th>Comments</th>
									<th>Favorite Posts</th>
									<th>Created At</th>
									<th>Actions</th>
								</tr>
								</tfoot>
								<tbody>
									@foreach($authors as $key => $author)
										<tr>
											<th>{{ $key + 1 }}</th>
											<th>{{ $author->name }}</th>
											<th>{{ $author->posts_count }}</th>
											<th>{{ $author->comments_count }}</th>
											<th>{{ $author->favorite_posts_count }}</th>
											<th>{{ $author->created_at->toFormattedDateString() }}</th>
											<th class="text-center">
												<button class="btn btn-sm btn-danger waves-effect" type="button" onclick="deleteAuthor({{ $author->id }})">
													<i class="material-icons">delete</i>
												</button>
												<form id="delete-form-{{ $author->id }}" action="{{ route('admin.author.destroy',$author->id) }}" method="POST" style="display: none;">
													@csrf
													@method('DELETE')
												</form>
											</th>
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
		function deleteAuthor(id) {
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