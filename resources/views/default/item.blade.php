@extends('layout.app')

@section('title', 'Items Management')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Items Management</h5>
                    <button class="btn btn-primary" id="addItemBtn" data-bs-toggle="modal" data-bs-target="#itemModal">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="itemsTable" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Created At</th>
                                    <th width="15%">Actions</th>
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
<div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalLabel">Add New Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="itemForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="itemId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="categoryError"></span>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <span class="text-danger" id="nameError"></span>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <span class="text-danger" id="priceError"></span>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Item Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <span class="text-danger" id="imageError"></span>
                        <div id="imagePreview" class="mt-2" style="display:none;">
                            <img id="previewImage" src="#" alt="Preview" style="max-height: 100px;">
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
   


<!-- View Item Modal -->
<div class="modal fade" id="viewItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="itemDetailsContent">
                <!-- AJAX content will be loaded here -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this item? This action cannot be undone.</p>
                <input type="hidden" id="deleteItemId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Display Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Item QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCode"></div>
                <button class="btn btn-outline-primary mt-3" id="downloadQR">
                    <i class="fas fa-download"></i> Download QR Code
                </button>
            </div>
        </div>
    </div>
</div>

<div style="display: none;">
    Categories Count: {{ count($categories) }}<br>
    @foreach($categories as $cat)
        {{ $cat->id }} - {{ $cat->name }}<br>
    @endforeach
</div>

@endsection



@section('scripts')
<!-- QR Code Library -->
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable 
    var table = $('#itemsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('items.index') }}",
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex', 
                orderable: false, 
                searchable: false,
                className: 'text-center' 
            },
             {
    data: 'image_path', 
    name: 'image_path',
    className: 'text-center',
    render: function(data, type, row) {
        if (!data || data === '') {
            return '<div class="text-muted small">No image</div>';
        }
        return `
            <div class="table-image-container">
                <img src="/storage/public/${data}" 
                     class="img-thumbnail table-image"
                     alt="Item image"
                     onerror="this.onerror=null;this.src='/images/default-item.png';">
            </div>
        `;
    }
},
            { 
                data: 'name', 
                name: 'name',
                className: 'align-middle'
            },
            { 
                data: 'category', 
                name: 'category.name',
                className: 'align-middle'
            },
            { 
                data: 'price', 
                name: 'price',
                className: 'text-end',
                render: function(data, type, row) {
                   const price = Number(data);
        return isNaN(price) ? '$0.00' : '$' + price.toFixed(2);
                }
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                className: 'text-center',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'text-center',
                 render: function(data, type, row) {
        return `
            <div class="d-flex justify-content-center">
                <button class="btn btn-primary btn-action edit-btn" data-id="${row.id}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-action delete-btn" data-id="${row.id}">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button class="btn btn-info btn-action view-btn" data-id="${row.id}">
                    <i class="fas fa-eye"></i> Details
                </button>
            </div>
        `;
    }
            }
        ],
        columnDefs: [
            {
                targets: [0, 1, 4, 5, 6], // Target specific columns by index
                className: 'text-center' // Center align these columns
            },
            {
                targets: 2, // Name column
                className: 'align-middle' // Vertical align middle
            },
            {
                targets: 3, // Category column
                className: 'align-middle' // Vertical align middle
            }
        ],
        order: [[2, 'asc']], // Default sort by name column
        responsive: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>'
        }
    });

    // Initialize modals
    const itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
    const viewItemModal = new bootstrap.Modal(document.getElementById('viewItemModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    // Add Item Button Click Handler
    $('#addItemBtn').click(function() {
        // Reset form and set title for new item
        $('#itemForm')[0].reset();
        $('#categoryId').val(''); // Clear any hidden ID field
        $('#itemModalLabel').text('Add New Item');
        $('.text-danger').text(''); // Clear validation errors
        
        // Show the modal
        itemModal.show();
    });

    // View Item Button Click Handler
    $(document).on('click', '.view-btn', function() {
        var itemId = $(this).data('id');
        var viewUrl = "{{ route('items.show', ':id') }}".replace(':id', itemId);
        
        viewItemModal.show();
        
        $.get(viewUrl, function(response) {
            if (response.success) {
                $('#itemDetailsContent').html(response.html);
            } else {
                $('#itemDetailsContent').html(`
                    <div class="alert alert-danger">
                        ${response.message}
                    </div>
                `);
            }
        }).fail(function() {
            $('#itemDetailsContent').html(`
                <div class="alert alert-danger">
                    Failed to load item details
                </div>
            `);
        });
    });

    // Form Submission Handler
// Form Submission Handler
// Form Submission Handler
$('#itemForm').submit(function(e) {
    e.preventDefault();
    $('.text-danger').text('');
    
    // Create FormData object
    const formData = new FormData(this);
    
    // For PUT requests, we need to append the _method field
    if ($('#itemId').val()) {
        formData.append('_method', 'PUT');
    }

    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
    
    const url = $('#itemId').val() ? `/items/${$('#itemId').val()}` : "{{ route('items.store') }}";
    
    $.ajax({
        url: url,
        type: 'POST', // Always use POST, we handle PUT via _method
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#itemModal').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                
                $('#itemForm')[0].reset();
                $('#imagePreview').hide();
                table.ajax.reload(null, false); 
                toastr.success(response.message);
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                for (const field in errors) {
                    $(`#${field}Error`).text(errors[field][0]);
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
    // QR Code Generation Function
    function generateQRCode(itemData) {
        const qrContent = `Item: ${itemData.name}\nCategory: ${itemData.category_name}\nPrice: $${itemData.price}\nImage: ${itemData.image_url}`;
        
        $('#qrCode').empty();
        new QRCode(document.getElementById("qrCode"), {
            text: qrContent,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        $('#qrCodeContainer').show();
    }

// Edit Item Button Handler
$(document).on('click', '.edit-btn', function() {
    const itemId = $(this).data('id');
    const editUrl = `/items/${itemId}/edit`;
    
    const btn = $(this);
    const originalHtml = btn.html();
    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
    
    $.get(editUrl, function(response) {
        if (response.success) {
            $('#itemId').val(response.data.id);
            $('#category_id').val(response.data.category_id);
            $('#name').val(response.data.name);
            $('#price').val(parseFloat(response.data.price).toFixed(2)); // Ensure proper decimal format
            
            // Handle image preview
            if (response.data.image_path) {
                $('#imagePreview').show();
                $('#previewImage').attr('src', '/storage/' + response.data.image_path);
            } else {
                $('#imagePreview').hide();
            }
            $('.text-danger').text('');
            
            $('#itemModalLabel').text('Edit Item');
            itemModal.show();
        }
    }).fail(function(xhr) {
        toastr.error(xhr.responseJSON?.message || 'Failed to load item data');
    }).always(function() {
        btn.html(originalHtml).prop('disabled', false);
    });
});

// Delete Button Handler
$(document).on('click', '.delete-btn', function() {
    const itemId = $(this).data('id');
    $('#deleteItemId').val(itemId);
    deleteModal.show();
});

// Confirm Delete Handler
$('#confirmDelete').click(function() {
    const itemId = $('#deleteItemId').val();
    const deleteUrl = `/items/${itemId}`;
    
    // Show loading state
    const btn = $(this);
    const originalHtml = btn.html();
    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
    
    $.ajax({
        url: deleteUrl,
        type: 'DELETE',
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if (response.success) {
                deleteModal.hide();
                table.ajax.reload(null, false);
                toastr.success(response.message);
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Failed to delete item');
        },
        complete: function() {
            btn.html(originalHtml).prop('disabled', false);
        }
    });
});

// Image Preview Handler
$('#image').change(function() {
    const file = this.files[0];
    if (!file) return;
    
    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (!validTypes.includes(file.type)) {
        $('#imageError').text('Only JPEG, PNG, JPG, and GIF images are allowed');
        $(this).val('');
        return;
    }
    
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
        $('#imageError').text('Image size must be less than 2MB');
        $(this).val('');
        return;
    }
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        $('#imagePreview').show();
        $('#previewImage').attr('src', e.target.result);
    }
    reader.readAsDataURL(file);
    
    $('#imageError').text('');
});
    
    // Download QR Code Handler
    $('#downloadQR').click(function() {
        const canvas = document.querySelector('#qrCode canvas');
        const link = document.createElement('a');
        link.download = 'item-qr-code.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    });

   
});


</script>
@endsection