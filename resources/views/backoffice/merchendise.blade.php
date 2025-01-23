@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Merchandise Management</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMerchandiseModal">
                <i class="bi bi-plus-circle me-2"></i>Add Merchandise
            </button>
        </div>
        <div class="card-body">
            <table id="merchandiseTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="createMerchandiseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Merchandise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createMerchandiseForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="text" class="form-control" name="image">
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

    <div class="modal fade" id="editMerchandiseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Merchandise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editMerchandiseForm">
                    <input type="hidden" name="id" id="editMerchandiseId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="editMerchandiseName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editMerchandiseDescription" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" name="price" id="editMerchandisePrice" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="text" class="form-control" name="image" id="editMerchandiseImage">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const merchandiseTable = $('#merchandiseTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('merchandise.index') }}",
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { 
                data: 'price', 
                name: 'price',
                render: function(data) {
                    return '$' + parseFloat(data).toFixed(2);
                }
            },
            {
                data: 'id',
                render: function(data) {
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-primary edit-btn" data-id="${data}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">Delete</button>
                        </div>
                    `;
                },
                orderable: false,
                searchable: false
            }
        ]
    });

    $('#createMerchandiseForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('merchandise.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#createMerchandiseModal').modal('hide');
                merchandiseTable.ajax.reload();
                Swal.fire('Success', response.message, 'success');
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
    });

    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.get(`/merchandise/${id}`, function(data) {
            $('#editMerchandiseId').val(data.id);
            $('#editMerchandiseName').val(data.name);
            $('#editMerchandiseDescription').val(data.description);
            $('#editMerchandisePrice').val(data.price);
            $('#editMerchandiseImage').val(data.image);
            $('#editMerchandiseModal').modal('show');
        });
    });

    $('#editMerchandiseForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#editMerchandiseId').val();
        $.ajax({
            url: `/merchandise/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                $('#editMerchandiseModal').modal('hide');
                merchandiseTable.ajax.reload();
                Swal.fire('Success', response.message, 'success');
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
    });

    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'You cannot revert this action!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/merchandise/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        merchandiseTable.ajax.reload();
                        Swal.fire('Deleted!', response.message, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseText, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush