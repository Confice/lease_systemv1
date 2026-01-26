@extends('layouts.admin_app')

@section('title', 'Add Marketplace')

@section('content')
<div class="row">
    <div class="col-12 col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add New Marketplace</h5>
            </div>
            <div class="card-body">
                <form id="marketplaceForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <!-- Marketplace Name -->
                        <div class="col-12">
                            <label class="form-label">Marketplace Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-store"></i></span>
                                <input type="text" name="marketplace" class="form-control" placeholder="Enter marketplace name" required>
                            </div>
                            <div class="invalid-feedback d-block" data-error="marketplace"></div>
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label class="form-label">Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-map"></i></span>
                                <textarea name="marketplaceAddress" class="form-control" rows="3" placeholder="Enter complete address" required></textarea>
                            </div>
                            <div class="invalid-feedback d-block" data-error="marketplaceAddress"></div>
                        </div>

                        <!-- Logo Upload -->
                        <div class="col-12">
                            <label class="form-label">Logo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-image"></i></span>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                            </div>
                            <div class="form-text">Maximum file size: 2MB. Allowed formats: JPEG, PNG, JPG, GIF, SVG</div>
                            <div class="invalid-feedback d-block" data-error="logo"></div>
                        </div>

                        <!-- Facebook Link -->
                        <div class="col-12">
                            <label class="form-label">Facebook Link</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bxl-facebook"></i></span>
                                <input type="url" name="facebookLink" class="form-control" placeholder="https://facebook.com/...">
                            </div>
                            <div class="invalid-feedback d-block" data-error="facebookLink"></div>
                        </div>

                        <!-- Telephone Number -->
                        <div class="col-12">
                            <label class="form-label">Telephone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                <input type="text" name="telephoneNo" class="form-control" placeholder="Enter telephone number">
                            </div>
                            <div class="invalid-feedback d-block" data-error="telephoneNo"></div>
                        </div>

                        <!-- Viber Number -->
                        <div class="col-12">
                            <label class="form-label">Viber Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-chat"></i></span>
                                <input type="text" name="viberNo" class="form-control" placeholder="Enter viber number">
                            </div>
                            <div class="invalid-feedback d-block" data-error="viberNo"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-label-primary" onclick="window.history.back()">
                            <i class="bx bx-x me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Save Marketplace
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="{{ asset('sneat/assets/css/users-page-improvements.css') }}">

<script>
$(function(){
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $('#marketplaceForm').on('submit', function(e){
        e.preventDefault();
        $('[data-error]').text('');
        $('#marketplaceForm').find('.is-invalid').removeClass('is-invalid');
        $('#marketplaceForm').find('.input-group').removeClass('error-state');

        let formData = new FormData(this);
        let $form = $(this);
        let $btn = $form.find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: "{{ route('admins.marketplaces.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
                Swal.fire({
                    icon:'success',
                    title:'Success',
                    text: 'Marketplace added successfully!',
                    toast:true,
                    position:'top',
                    showConfirmButton:false,
                    showCloseButton:true,
                    timer: 2000,
                    timerProgressBar:true
                }).then(() => {
                    window.location.href = "{{ route('admins.dashboard') }}";
                });
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors || {};
                    $.each(errors, function(field, messages){
                        $('[data-error="'+field+'"]').text(messages[0]);
                        $('[name="'+field+'"]').addClass('is-invalid');
                        $('[name="'+field+'"]').closest('.input-group').addClass('error-state');
                    });
                } else {
                    let errorMessage = xhr.responseJSON?.message || 'Something went wrong. Please try again.';
                    Swal.fire({
                        icon:'error',
                        title:'Error',
                        text: errorMessage,
                        toast:true,
                        position:'top',
                        showConfirmButton:false,
                        showCloseButton:true,
                        timer: 2000,
                        timerProgressBar:true
                    });
                }
                $btn.prop('disabled', false);
            }
        });
    });

    // Clear validation errors when user types/changes input
    $('#marketplaceForm').on('input change', '.form-control', function(){
        const $input = $(this);
        $input.removeClass('is-invalid');
        $input.closest('.input-group').removeClass('error-state');
        $('[data-error="'+$input.attr('name')+'"]').text('');
    });
});
</script>
@endpush

