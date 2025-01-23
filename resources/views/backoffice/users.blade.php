@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">User Management</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-plus-circle me-2"></i>Add User
            </button>
        </div>
        <div class="card-body">
            <table id="usersTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createUserForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*" id="createImageInput">
                            <img id="createImagePreview" src="" class="img-fluid mt-2" style="display:none; max-height: 200px;">
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

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editUserId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="editUserName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="editUserEmail" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*" id="editImageInput">
                            <img id="editImagePreview" src="" class="img-fluid mt-2" style="display:none; max-height: 200px;">
                            <div id="currentImageContainer" class="mt-2">
                                <img id="currentImage" src="" class="img-fluid" style="max-height: 200px;">
                            </div>
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
    const usersTable = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('users.index') }}",
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'image', 
                name: 'image',
                render: function(data) {
                    return data 
                        ? `<img src="{{ asset('storage/') }}/${data}" width="50" height="50" class="rounded-circle">`
                        : 'No Image';
                }
            },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
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

    $('#createImageInput').on('change', function(e) {
        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onload = function(event) {
            $('#createImagePreview')
                .attr('src', event.target.result)
                .show();
        };
        reader.readAsDataURL(file);
    });

    $('#editImageInput').on('change', function(e) {
        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onload = function(event) {
            $('#editImagePreview')
                .attr('src', event.target.result)
                .show();
        };
        reader.readAsDataURL(file);
    });

    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('users.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#createUserModal').modal('hide');
                usersTable.ajax.reload();
                Swal.fire('Success', response.message, 'success');
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                const errorMessage = Object.values(errors).flat().join('<br>');
                Swal.fire('Error', errorMessage, 'error');
            }
        });
    });

    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.get(`/users/${id}`, function(data) {
            $('#editUserId').val(data.id);
            $('#editUserName').val(data.name);
            $('#editUserEmail').val(data.email);
            
            if (data.image) {
                $('#currentImage').attr('src', `{{ asset('storage/') }}/${data.image}`);
                $('#currentImageContainer').show();
            } else {
                $('#currentImageContainer').hide();
            }

            $('#editImagePreview').attr('src', '').hide();
            
            $('#editUserModal').modal('show');
        });
    });

    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#editUserId').val();
        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        $.ajax({
            url: `/users/${id}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#editUserModal').modal('hide');
                usersTable.ajax.reload();
                Swal.fire('Success', response.message, 'success');
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                const errorMessage = Object.values(errors).flat().join('<br>');
                Swal.fire('Error', errorMessage, 'error');
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
                    url: `/users/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        usersTable.ajax.reload();
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