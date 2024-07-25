  <h4 class="text-center">Summary</h4>
  <p class="text-center">Please review your details before proceeding.</p>
  {{-- <div class="row">
      <div class="col-lg-6">
          <div class="mb-3">
              <strong>First Name:</strong>
              <p id="summary-first_name"></p>
          </div>
      </div>
      <div class="col-lg-6">
          <div class="mb-3">
              <strong>Last Name:</strong>
              <p id="summary-last_name"></p>
          </div>
      </div>
      <div class="col-lg-6">
          <div class="mb-3">
              <strong>Username:</strong>
              <p id="summary-username"></p>
          </div>
      </div>
      <div class="col-lg-6">
          <div class="mb-3">
              <strong>Email:</strong>
              <p id="summary-email"></p>
          </div>
      </div>
      <div class="col-lg-6">
          <div class="mb-3">
              <strong>Country:</strong>
              <p id="summary-country"></p>
          </div>
      </div>
      <div class="col-lg-6">
          <div class="mb-3">
              <strong>Phone Number:</strong>
              <p id="summary-phone_number"></p>
          </div>
      </div>
  </div> --}}
  <div class="row">
      <div class="col-lg-6 mb-3 form-group">
          <label for="first_name">First Name</label>
          <input type="text" class="form-control" name="first_name" id="summary-first_name" required>
      </div>
      <div class="col-lg-6 mb-3 form-group">
          <label for="last_name">Last Name</label>
          <input type="text" class="form-control" name="last_name" id="summary-last_name" required>
      </div>
  </div>
  <div class="row">
      <div class="mb-3 col-lg-6 form-group">
          <label for="email">Email</label>
          <input type="email" class="form-control" name="email" id="summary-email" required>
      </div>
      <div class="mb-3 col-lg-6 form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" name="username" id="summary-username" required>
      </div>
  </div>
  <div class="row">
      <div class="mb-3 col-lg-6 form-group">
          <label for="country">Country</label>
          <select class="form-control" name="country" required id="summary-country">
              <option value="">Select</option>
              @foreach ($countries as $item)
                  <option value="{{ $item->iso2 }}" data-countryName="{{ $item->name }}">{{ $item->name }}
                  </option>
              @endforeach
          </select>
          <div class="invalid-feedback">Please select a country.</div>
      </div>
      <div class="mb-3 col-lg-6 form-group">
          <label for="phone_number">Phone Number</label>
          <input type="text" class="form-control" name="phone_number" id="summary-phone_number" required>
      </div>
  </div>
  <h6>Package:</h6>
  <div class="table-responsive mb-4">
      <table class="table text-nowrap">
          <tbody>
              <tr>
                  <td>
                      <div class="d-flex align-items-center">
                          <div class="me-3">
                              <span class="avatar avatar-xxl bg-light">
                                  <img src="" id="preview-image" alt="">
                              </span>
                          </div>
                          <div>
                              <div class="mb-1 fs-14 fw-semibold">
                                  <a href="javascript:void(0);" id="preview-name"></a>
                              </div>
                              <div class="mb-1">
                                  <span class="fs-15 fw-semibold" id="preview-price"></span>
                              </div>
                          </div>
                      </div>
                  </td>
                  <td></td>
              </tr>
              <tr>
                  <td colspan="2"></td>
                  <td colspan="2">
                      <div class="fw-semibold">Total Price :</div>
                  </td>
                  <td>
                      <span class="fs-16 fw-semibold" id="preview-total-price"></span>
                  </td>
              </tr>
          </tbody>
      </table>
  </div>
  <h6>Payment Methods:</h6>
  <div class="fs-15 fw-semibold d-sm-flex d-block align-items-center justify-content-between mb-3">
      <x-payment-method />
  </div>
