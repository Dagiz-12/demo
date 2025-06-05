@extends('layout.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Categories Management</h5>
                    <button class="btn btn-primary" id="addCategoryBtn" data-toggle="modal" data-target="#categoryModal">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="categoriesTable" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th width="10%">#</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th width="20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->

<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId" name="id">
               
              <div class="modal-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <span class="text-danger" id="nameError"></span>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    <span class="text-danger" id="statusError"></span>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this category?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')

<style>
    .btn-action {
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        margin: 0 3px;
    }
    .dataTables_filter {
        float: right;
        margin-bottom: 1em;
    }
    .dataTables_filter input {
        margin-left: 0.5em;
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.35em 0.65em;
        font-weight: 500;
    }
    .bg-success {
        background-color: #28a745 !important;
    }
    .bg-danger {
        background-color: #dc3545 !important;
    }
    #categoriesTable th {
        white-space: nowrap;
    }
     .badge {
        font-size: 0.95rem;
        padding: 0.5em 0.75em;
        font-weight: 600;
        min-width: 80px;
        display: inline-block;
        text-align: center;
    }
    #categoriesTable th {
        white-space: nowrap;
        vertical-align: middle;
    }
    #categoriesTable td {
        vertical-align: middle;
    }


</style>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#categoriesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('categories.index') }}",
            type: "GET",
           
           error: function(xhr) {
                console.error('DataTables error:', xhr.responseText);
                toastrMessage('error', 'Failed to load data', 'Error');
            }
        },
        columns: [
            { 
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row, meta) {
                    
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
                
            },
                    { 
                data: 'name', 
                name: 'name',
                orderable: true,
                searchable: true
            },
            { 
                data: 'status', 
                name: 'status',
                orderable: true,
                searchable: true,
                className: 'text-center',
                
            render: function(data, type, row) {
            // Double protection for null status
            const status = data || 'Active'; // Only fallback if completely null/undefined
            const badgeClass = status.toLowerCase() === 'active' ? 'bg-success' : 'bg-danger';
            return `<span class="badge ${badgeClass} p-2">${status}</span>`;
        }
              
        },

            
            { 
                data: 'id',  
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'text-center',
                render: function(data) {
            return `
                <div class="d-flex justify-content-center">
                    <button class="btn btn-primary btn-lg mx-1 edit-btn" data-id="${data}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-lg mx-1 delete-btn" data-id="${data}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            `;
        
                }
            }
        ],

        columnDefs: [
    {
        targets: [0, 2, 3], 
        className: 'text-center align-middle'
    },
    {
        targets: 1, // Name column
        className: 'align-middle'
    }
    
],


        order: [[1, 'asc']], // Default sort by name column
        responsive: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>'
        }
    });

    // modal handlers
    $('#categoryModal').on('hidden.bs.modal', function() {
        $('#categoryForm')[0].reset();
        $('#categoryId').val('');
        $('#categoryModalLabel').text('Add New Category');
        $('.text-danger').text('');
    });

     // Add Category Button
    $('#addCategoryBtn').click(function() {
        $('#categoryModal').modal('show');
    });

    // Edit Category Button Click
$(document).on('click', '.edit-btn', function() {
    var id = $(this).data('id');
    
    // Show loading state
    var btn = $(this);
    var originalHtml = btn.html();
    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
    
    // Use proper route generation
    var editUrl = "{{ route('categories.edit', ':id') }}".replace(':id', id);
    
    $.get(editUrl, function(response) {
        $('#categoryModalLabel').text('Edit Category');
        $('#categoryId').val(response.data.id);
        $('#name').val(response.data.name);
        $('#status').val(response.data.status); 
        $('#categoryModal').modal('show');
    })
    .fail(function() {
        toastr.error('Failed to load category data', 'Error');
    })
    .always(function() {
        btn.html(originalHtml).prop('disabled', false);
    });
});
    // Form Submission
$('#categoryForm').submit(function(e) {
    e.preventDefault();
    
    // Clear previous errors
    $('.text-danger').text('');
    
    // Get form data
    var formData = $(this).serialize();
    var categoryId = $('#categoryId').val();
    
    // Determine URL and method
    var url = categoryId 
        ? `/categories/${categoryId}` // Update existing
        : '/categories'; // Create new
    
    var method = categoryId ? 'PUT' : 'POST';
    
    // Show loading state
    var submitBtn = $('#saveCategoryBtn');
    var originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
    // In the categoryForm submit handler, before the AJAX call
console.log('Form status value:', $('#status').val()); // Debug the selected status
    
    $.ajax({
        url: url,
        type: method,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#categoryModal').modal('hide');
                $('#categoriesTable').DataTable().ajax.reload(null, false);
                toastr.success(response.message);
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                // Validation errors
                var errors = xhr.responseJSON.errors;
                for (var field in errors) {
                    $('#' + field + 'Error').text(errors[field][0]);
                }
            } else {
                toastr.error(xhr.responseJSON?.message || 'An error occurred');
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});

    // Delete Button Click
    var deleteId;
    $(document).on('click', '.delete-btn', function() {
        deleteId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

  // Delete Confirmation
$('#confirmDelete').click(function() {
    var btn = $(this);
    var originalHtml = btn.html();
    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
    
    // Use proper route generation
    var deleteUrl = "{{ route('categories.destroy', ':id') }}".replace(':id', deleteId);
    
    $.ajax({
        url: deleteUrl,
        type: 'DELETE',
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            $('#deleteModal').modal('hide');
            table.ajax.reload(null, false);
            toastr.success(response.message, 'Success');
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Failed to delete category', 'Error');
        },
        complete: function() {
            btn.html(originalHtml).prop('disabled', false);
        }
    });
});

     // Toastr helper function
    function toastrMessage(type, message, title) {
        toastr[type](message, title, {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 5000
        });
    }
});
</script>
@endsection