<div class="container-fluid">
<div class="row">
    <div class="col-sm-12">
        <!-- Success message -->
        @if (session('success'))
            <div class="row">
                <div class="alert alert-success alert-dismissible"> <!-- Use alert-success for success messages -->
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Warning/Error message -->
        @if (session('message'))
            <div class="row">
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <strong>Warning!</strong> {{ session('message') }}
                </div>
            </div>
        @endif

        <!-- Error message -->
        @if (session('error'))
            <div class="row">
                <div class="alert alert-danger alert-dismissible"> <!-- Use alert-danger for errors -->
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <strong>Error!</strong> {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Validation errors -->
        @if ($errors->any())
            <div class="row">
                @foreach ($errors->all() as $error)
                    <div class="col-lg-12">
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            <strong>Error!</strong> {{ $error }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
</div>
