<form action="{{ route('kyc.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card custom-card">
        <div class="card-header justify-content-between">
            <div class="card-title">
                Upload Identity Card
            </div>
        </div>
        <div class="card-body">
            <div>
                <div class="text-center mb-3" id="frontContent">
                    <img src="{{ asset('assets/images/default.jpg') }}" class="img-fluid rounded" width="150px"
                        height="50px" alt="...">
                </div>
                <div class="mb-3">
                    <input class="form-control" type="file" id="front-photo" name="photo">
                </div>
            </div>
            <button class="btn btn-primary" type="submit">
                <div class="spinner-border" style="display: none" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <span id="text">Submit</span>
            </button>
        </div>
    </div>

</form>
