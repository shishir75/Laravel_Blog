@extends('layouts.backend.app')

@section('title', 'Tag')

@push('css')
	<!-- JQuery DataTable Css -->
	<link href="{{ asset('assets/backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}" rel="stylesheet">
@endpush

@section('content')
	<div class="container-fluid">
		<div class="block-header">
			<a class="btn btn-primary waves-effect" href="{{ route('admin.tag.create') }}">
				<i class="material-icons">add</i>
				<span>Add New Tag</span>
			</a>
		</div>

		<!-- Exportable Table -->
		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					<div class="header">
						<h2>
							ALL TAGS
							<span class="badge bg-blue">{{ $tags->count() }}</span>
						</h2>
					</div>
					<div class="body">
						<div class="table-responsive">
							<table class="table table-bordered table-striped table-hover dataTable js-exportable">
								<thead>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Posts Count</th>
									<th>Created At</th>
									<th>Updated At</th>
									<th>Actions</th>
								</tr>
								</thead>
								<tfoot>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Posts Count</th>
									<th>Created At</th>
									<th>Updated At</th>
									<th>Actions</th>
								</tr>
								</tfoot>
								<tbody>
									@foreach($tags as $key => $tag)
										<tr>
											<th>{{ $key + 1 }}</th>
											<th>{{ $tag->name }}</th>
											<th>{{ $tag->posts->count() }}</th>
											<th>{{ $tag->created_at }}</th>
											<th>{{ $tag->updated_at }}</th>
											<th class="text-center">
												<a href="{{ route('admin.tag.edit', $tag->id) }}" class="btn btn-sm btn-info">
													<i class="material-icons">edit</i>
												</a>
												<button class="btn btn-sm btn-danger waves-effect" type="button" onclick="deleteTag({{ $tag->id }})">
													<i class="material-icons">delete</i>
												</button>
												<form id="delete-form-{{ $tag->id }}" action="{{ route('admin.tag.destroy',$tag->id) }}" method="POST" style="display: none;">
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
		function deleteTag(id) {
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