@extends('layout.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Orders</h2>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#orderModal">
                Create New Order
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered" id="orders-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>POS</th>
                                <th>Date</th>
                                <th>Items Count</th>
                                <th>Total Quantity</th>
                                <th>Total Price</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Create/Edit Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="orderForm">
                <div class="modal-body">
                    <input type="hidden" id="order_id" name="order_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="pos1_id" class="form-label">POS</label>
                            <select class="form-select" id="pos1_id" name="pos1_id" required>
                                <option value="">Select POS</option>
                                @foreach($posList as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="order_date" class="form-label">Date & Time</label>
                            <input type="datetime-local" class="form-control" id="order_date" name="order_date" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="memo" class="form-label">Memo</label>
                        <textarea class="form-control" id="memo" name="memo" rows="3"></textarea>
                    </div>

                

                    <div class="table-responsive">
                    <table class="table table-bordered" id="order-items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            <!-- Items will be added here -->
                        </tbody>
                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="button" id="add-item-btn" class="btn btn-primary">Add New Item</button>
                    </div>
                </div>

                    <div class="mt-3">
                        <strong>Order Total:</strong> $<span id="order-total">0.00</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script> 
$(document).ready(function() {
    let availableItems = {!! json_encode($items) !!};
    let selectedItemIds = [];

    // Function to update available items dropdown
    function updateAvailableItems() {
        const remainingItems = availableItems.filter(item => 
            !selectedItemIds.includes(item.id)
        );
        return remainingItems;
    }

    // Initialize DataTable
    // Update the DataTable initialization
const table = $('#orders-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('orders.index') }}",
    columns: [
        { 
            data: null,
            name: 'serial',
            orderable: false,
            searchable: false,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        { 
            data: 'pos1.name', 
            name: 'pos1.name',
            render: function(data) {
                return data || '';
            }
        },
        { 
            data: 'order_date', 
            name: 'order_date',
            render: function(data) {
                return data ? new Date(data).toLocaleString() : '';
            }
        },
        { 
            data: 'items_count', 
            name: 'items_count',
            render: function(data) {
                return data || 0;
            }
        },
        { 
            data: 'total_quantity', 
            name: 'total_quantity',
            render: function(data) {
                return data || 0;
            }
        },
        { 
            data: 'total_price', 
            name: 'total_price',
            render: function(data) {
                return '$' + parseFloat(data || 0).toFixed(2);
            }
        },
        { 
            data: 'created_at', 
            name: 'created_at',
            render: function(data) {
                return data ? new Date(data).toLocaleString() : '';
            }
        },
        { 
            data: 'action', 
            name: 'action',
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
                return `
                    <button class="btn btn-primary btn-sm edit-order" data-id="${row.id}">Edit</button>
                    <button class="btn btn-danger btn-sm delete-order" data-id="${row.id}">Delete</button>
                `;
            }
        }
    ],
    order: [[1, 'asc']] // Order by the second column (POS name) by default
});

    // Reset form and open modal for new order
    $('#orderModal').on('show.bs.modal', function(e) {
        if (!$(e.relatedTarget).hasClass('edit-order')) {
            $('#orderForm')[0].reset();
            $('#order_id').val('');
            $('#items-container').empty();
            $('#order_date').val(new Date().toISOString().slice(0, 16));
            selectedItemIds = [];
            $('#order-total').text('0.00');
        }
    });

    // Add item to the form
    $('#add-item-btn').click(function() {
        const remainingItems = updateAvailableItems();
        
        if (remainingItems.length === 0) {
            showToast('No more items available to add', 'warning');
            return;
        }

        const newItem = $(`
            <tr class="item-row">
                <td>
                    <select class="form-select item-select" name="items[${selectedItemIds.length}][item_id]" required>
                        <option value="">Select Item</option>
                        ${remainingItems.map(item => `
                            <option value="${item.id}" data-price="${item.price}">${item.name}</option>
                        `).join('')}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control unit-price" name="items[${selectedItemIds.length}][unit_price]" readonly>
                </td>
                <td>
                    <input type="number" class="form-control quantity" name="items[${selectedItemIds.length}][quantity]" min="1" value="1" required>
                </td>
                <td>
                    <input type="text" class="form-control total-price" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                </td>
            </tr>
        `);

        $('#items-container').append(newItem);
    });

    // Handle item selection
    $(document).on('change', '.item-select', function() {
        const row = $(this).closest('tr');
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price') || 0;
        
        row.find('.unit-price').val(price.toFixed(2));
        row.find('.quantity').trigger('input');
        
        // Update selected items
        selectedItemIds = [];
        $('.item-select').each(function() {
            const val = $(this).val();
            if (val) selectedItemIds.push(parseInt(val));
        });
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        const row = $(this).closest('tr');
        row.remove();
        calculateOrderTotal();
        
        // Update selected items
        selectedItemIds = [];
        $('.item-select').each(function() {
            const val = $(this).val();
            if (val) selectedItemIds.push(parseInt(val));
        });
    });

    // Calculate totals when quantity changes
    $(document).on('input', '.quantity', function() {
        const row = $(this).closest('tr');
        const quantity = parseFloat($(this).val()) || 0;
        const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
        const total = quantity * unitPrice;
        
        row.find('.total-price').val(total.toFixed(2));
        calculateOrderTotal();
    });

    // Calculate order total
    function calculateOrderTotal() {
        let total = 0;
        $('.total-price').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#order-total').text(total.toFixed(2));
    }

    // Edit order
  // Edit order
// Edit order
$(document).on('click', '.edit-order', function() {
    const orderId = $(this).data('id');
    $.get(`/orders/${orderId}/edit`, function(data) {
        if (data.success && data.order) {
            const order = data.order;
            
            $('#orderModal').modal('show');
            
            // Reset form and clear items
            $('#orderForm')[0].reset();
            $('#items-container').empty();
            selectedItemIds = [];
            
            // Set parent order values
            $('#order_id').val(order.id);
            $('#pos1_id').val(order.pos1_id).trigger('change');
            $('#order_date').val(order.order_date);
            $('#memo').val(order.memo || '');
            
            // Add child items
            if (order.items && order.items.length > 0) {
                order.items.forEach((item, index) => {
                    selectedItemIds.push(item.item_id);
                    
                    // Safely handle numeric values
                    const unitPrice = parseFloat(item.unit_price) || 0;
                    const quantity = parseInt(item.quantity) || 1;
                    const totalPrice = parseFloat(item.total_price) || 0;
                    
                    const newItem = $(`
                        <tr class="item-row">
                            <td>
                                <select class="form-select item-select" name="items[${index}][item_id]" required>
                                    <option value="">Select Item</option>
                                    ${availableItems.map(availItem => `
                                        <option value="${availItem.id}" data-price="${availItem.price}" ${availItem.id == item.item_id ? 'selected' : ''}>
                                            ${availItem.name}
                                        </option>
                                    `).join('')}
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control unit-price" name="items[${index}][unit_price]" value="${unitPrice.toFixed(2)}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control quantity" name="items[${index}][quantity]" min="1" value="${quantity}" required>
                            </td>
                            <td>
                                <input type="text" class="form-control total-price" value="${totalPrice.toFixed(2)}" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                            </td>
                        </tr>
                    `);
                    
                    $('#items-container').append(newItem);
                });
            }
            
            calculateOrderTotal();
        } else {
            showToast(data.message || 'Error loading order data', 'error');
        }
    }).fail(function(xhr) {
        showToast('Failed to load order data: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
    });
});

    // Save order (create/update)
    $('#orderForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const orderId = $('#order_id').val();
        const url = orderId ? `/orders/${orderId}` : '/orders';
        const method = orderId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // In your index.blade.php, update the success callback in the form submit handler
            success: function(response) {
                if (response.success) {
                    $('#orderModal').modal('hide');
                    // Clear the form
                    $('#orderForm')[0].reset();
                    $('#items-container').empty();
                    selectedItemIds = [];
                    $('#order-total').text('0.00');
                    
                    // Properly reload the DataTable
                    $('#orders-table').DataTable().ajax.reload(function() {
                        showToast(response.message, 'success');
                    }, false);
                } else {
                    showToast(response.message || 'Error saving order', 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error saving order';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showToast(errorMsg, 'error');
            }
        });
    });

    // Delete order
    $(document).on('click', '.delete-order', function() {
        const orderId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this order?')) {
            $.ajax({
                url: `/orders/${orderId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    table.ajax.reload();
                    showToast(response.success ? 'Order deleted successfully' : 'Error deleting order', 
                             response.success ? 'success' : 'error');
                },
                error: function(xhr) {
                    showToast('Error: ' + xhr.responseJSON.message, 'error');
                }
            });
        }
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        const toast = `<div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>`;
        
        $('.toast-container').append(toast);
        $('.toast').toast('show');
        
        setTimeout(() => {
            $('.toast').toast('hide').remove();
        }, 5000);
    }

    // Reset edit mode flag when modal hides
    $('#orderModal').on('hidden.bs.modal', function() {
        $(this).removeData('edit-mode');
    });
});
</script>
@endpush