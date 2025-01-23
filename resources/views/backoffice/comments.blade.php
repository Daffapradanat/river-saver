@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Comments Management</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCommentModal">
                <i class="bi bi-plus-circle me-2"></i>Add Comment
            </button>
        </div>
        <div class="card-body">
            <table id="commentsTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="createCommentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createCommentForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" required></textarea>
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

    <!-- Edit Comment Modal -->
    <div class="modal fade" id="editCommentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCommentForm">
                    <input type="hidden" name="id" id="editCommentId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="editCommentName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editCommentDescription" required></textarea>
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
    const commentsTable = $('#commentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('comments.index') }}",
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
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

    $('#createCommentForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('comments.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#createCommentModal').modal('hide');
                commentsTable.ajax.reload();
                Swal.fire('Success', 'Comment created successfully', 'success');
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON.message || 'An error occurred', 'error');
            }
        });
    });

    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.get(`/comments/${id}`, function(data) {
            $('#editCommentId').val(data.id);
            $('#editCommentName').val(data.name);
            $('#editCommentDescription').val(data.description);
            $('#editCommentModal').modal('show');
        });
    });

    $('#editCommentForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#editCommentId').val();
        $.ajax({
            url: `/comments/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                $('#editCommentModal').modal('hide');
                commentsTable.ajax.reload();
                Swal.fire('Success', 'Comment updated successfully', 'success');
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON.message || 'An error occurred', 'error');
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
                    url: `/comments/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        commentsTable.ajax.reload();
                        Swal.fire('Deleted!', 'Comment deleted successfully', 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message || 'An error occurred', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush